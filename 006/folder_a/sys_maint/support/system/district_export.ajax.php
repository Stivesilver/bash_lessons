<?php

	Security::init(MODE_JQ);


	CoreUtils::increaseTime(36000);
	CoreUtils::increaseMemory();

	$type = io::post('type');

	if ($type == 1) {
		$sdata = db::execSQL("
			SELECT stdschid,
				   stdfnm,
				   stdlnm,
				   " . IDEAParts::get('disability') . " as disability,
				   (SELECT COALESCE(dccode,'') || ' - ' || dc.dcdesc
					  FROM webset.std_disabilitymst AS dm
					       INNER JOIN webset.statedef_disablingcondition dc ON dc.dcrefid = dm.dcrefid
					 WHERE dm.sdtype = 2
				       AND dm.stdrefid = ts.tsrefid
					 LIMIT 1) AS secdisability,
				   '',
				   '',
				   '',
				   '',
				   '',
				   '',
				   CASE WHEN stdcmpltdt > current_date - INTERVAL '50 years' AND stdcmpltdt < current_date THEN TO_CHAR(ts.stdcmpltdt, 'MM/DD/YYYY') ELSE '' END,
				   " . IDEAParts::get('stdenrolldt') . " as stdenrolldt,
				   " . IDEAParts::get('stdcmpltdt') . " as stdcmpltdt
			  FROM webset.sys_teacherstudentassignment ts
				   " . IDEAParts::get('studentJoin') . "
				   " . IDEAParts::get('gradeJoin') . "
				   " . IDEAParts::get('casemanJoin') . "
				   " . IDEAParts::get('schoolJoin') . "
				   " . IDEAParts::get('enrollJoin') . "
			 WHERE std.vndrefid = VNDREFID
			 ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
		")->indexAll();

		$stdfile = FileCSV::factory();
		$stdfile->addLine(
			array(
				'StudentCode',
				'FirstName',
				'LastName',
				'PrimaryDisability',
				'SecondaryDisability',
				'ReferralDate',
				'ReferralSource',
				'ParentConsentDate',
				'Eligibility',
				'EligibilityDate',
				'Triennial Date',
				'LastIEPDate',
				'IEPBeginDate',
				'IEPEndDate'
			)
		);

		$stdfile->addLines($sdata);

		$stdfile->save();
		$path = $stdfile->getPath();

		$npath = '/home/evsc/exports/students.csv';
		rename($path, $npath);
		JobQueueUtils::finish('Student Export Completed', JobQueueUtils::JQ_SUCCESS);
	} elseif ($type == 2) {
		$pdata = db::execSQL("
		SELECT COALESCE(gd.gdfnm, ''),
			   CASE WHEN gd.gdtype IN (17,2,4,28) THEN 'F'
		            WHEN gd.gdtype IN (16,1,3) THEN 'M'
		            WHEN gd.gdtype IN (12,19,7,8,33) THEN 'LG'
		            WHEN gd.gdtype = 18 THEN 'S'
		            WHEN gd.gdtype = 29 THEN 'ESP'
		            WHEN gd.gdtype IN (35,39,200003,27,10,9,40,50,49,51,53,26,200004,52,36) THEN 'OR'
		            WHEN gd.gdtype IN (31,32,37,22,23) THEN 'PAREP'
		            ELSE 'OTH'
		       END AS ptype,
		       COALESCE(gd.gdlnm, ''),
		       std.stdschid,
		       gd.gdadr1 || '; ' || gd.gdadr2 AS address,
		       gd.gdcity,
		       gd.gdstate,
			   gd.gdcitycode,
		       gd.gdhphn,
		       '',
		       '',
		       ''
		  FROM webset.sys_teacherstudentassignment AS ts
		       INNER JOIN webset.dmg_studentmst AS std ON ts.stdrefid = std.stdrefid
		       INNER JOIN webset.dmg_guardianmst AS gd ON (gd.stdrefid = std.stdrefid)
		WHERE std.vndrefid = VNDREFID
	    ORDER BY UPPER(gd.gdlnm), UPPER(gd.gdfnm)
	")->indexAll();

		$stdfile = FileCSV::factory();
		$stdfile->addLine(
			array(
				'ParentName',
				'ParentType',
				'Last Name',
				'StudentCode',
				'Address',
				'City',
				'State',
				'ZipCode',
				'HomePhone',
				'OnIEPTeam',
				'StudentLivesHere',
				'Guardian'
			)
		);

		$stdfile->addLines($pdata);

		$stdfile->save();
		$path = $stdfile->getPath();

		$npath = '/home/evsc/exports/parents.csv';
		rename($path, $npath);

		JobQueueUtils::finish('Parent Export Completed', JobQueueUtils::JQ_SUCCESS);
	} elseif ($type == 3) {
		$fdata = db::execSQL("
			SELECT iep.siepmdocfilenm,
			       COALESCE(std.stdlnm, '') || '_' || COALESCE(std.stdfnm, '') || '_' || COALESCE(std.stdschid, '') AS dname
			  FROM webset.std_iep AS iep
			       INNER JOIN webset.sys_teacherstudentassignment AS ts ON (ts.tsrefid = iep.stdrefid)
			       INNER JOIN webset.dmg_studentmst AS std ON (ts.stdrefid = std.stdrefid)
		     WHERE std.vndrefid = VNDREFID
		     ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
		")->assocAll();

		foreach ($fdata AS $fitem) {
			if ($fitem['siepmdocfilenm']) {
				if (!file_exists('/home/evsc/exports/files/' . $fitem['dname'])) {
					mkdir('/home/evsc/exports/files/' . $fitem['dname'], 0777);
				}
				copy(SystemCore::$secDisk . '/Iep/' . $fitem['siepmdocfilenm'], '/home/evsc/exports/files/' . $fitem['dname'] . '/' . $fitem['siepmdocfilenm']);
			}
		}
		JobQueueUtils::finish('IEP File Export Completed', JobQueueUtils::JQ_SUCCESS);
	} elseif ($type == 4) {
		include(SystemCore::$physicalRoot . "/applications/webset/iep/evalforms/frm_include.php");
		$docdata = db::execSQL("
			SELECT frm.smfcrefid,
				   frm.uploaded_file,
				   frm.fdf_content,
				   frm.archived,
				   state.mfcfilename,
				   frm.smfcfilename,
			       COALESCE(std.stdlnm, '') || '_' || COALESCE(std.stdfnm, '') || '_' || COALESCE(std.stdschid, '') AS dname
			  FROM webset.std_forms AS frm
			       INNER JOIN webset.sys_teacherstudentassignment AS ts ON (ts.tsrefid = frm.stdrefid)
			       INNER JOIN webset.dmg_studentmst AS std ON (ts.stdrefid = std.stdrefid)
			       LEFT JOIN webset.statedef_forms AS state ON (frm.mfcrefid = state.mfcrefid)
		     WHERE std.vndrefid = VNDREFID
		     ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm);
		")->assocAll();

		foreach ($docdata AS $ditem) {
			if (!file_exists('/home/evsc/exports/files/' . $ditem['dname'])) {
				mkdir('/home/evsc/exports/files/' . $ditem['dname'], 0777);
			}
			if (!file_exists('/home/evsc/exports/files/' . $ditem['dname'] . '/documentation')) {
				mkdir('/home/evsc/exports/files/' . $ditem['dname'] . '/documentation', 0777);
			}
			if ($ditem['uploaded_file']) {
				copy(SystemCore::$secDisk . '/Iep/' . $ditem['uploaded_file'], '/home/evsc/exports/files/' . $ditem['dname'] . '/documentation/' . $ditem['uploaded_file']);
			} else {
				$text = gen_pdf(base64_decode($ditem['fdf_content']));
				$fp = fopen('/home/evsc/exports/files/' . $ditem['dname'] . '/documentation/' . $ditem['smfcrefid'] . '_' . basename($ditem['mfcfilename']), "w");
				fputs($fp, $text);
				fclose($fp);
			}
		}
		JobQueueUtils::finish('Documentation File Export Completed', JobQueueUtils::JQ_SUCCESS);
	} elseif ($type == 5) {
		io::progress(null, 'Deleting old files...', true);
		exec('rm -rf /home/evsc/exports/files/*');
		JobQueueUtils::finish('Clear Files Folder Completed', JobQueueUtils::JQ_SUCCESS);
	}
?>
