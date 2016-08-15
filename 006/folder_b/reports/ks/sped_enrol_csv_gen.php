<?php

	Security::init(MODE_WS, 2);
	CoreUtils::increaseMemory();
	define('ENTERDATE', "COALESCE(stdenterdt, (CASE WHEN stdexitdt IS NOT NULL THEN '9999-12-12 00:00:00' ELSE '0001-01-01 00:00:00' END)::TIMESTAMP)::DATE");

	db::execSQL("
		CREATE TEMPORARY TABLE tmp_teacherstudentassignment
		AS
		SELECT stdrefid, MAX(" . ENTERDATE . ") AS dt
	   	  FROM webset.sys_teacherstudentassignment
		 WHERE stdrefid IS NOT NULL
		 GROUP BY 1;

		CREATE UNIQUE INDEX tmp_teacherstudentassignmenth_idx1 ON tmp_teacherstudentassignment USING btree (stdrefid,dt);
	");

	$data = db::execSQL("
		SELECT std.stdRefID AS stdrefid,
			   stdschid,
		       CASE WHEN ts.tsrefid > 0 THEN CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END ELSE 'I' END as spedstatus,
		       CASE WHEN ts.tsrefid > 0 THEN TO_CHAR(stdenterdt, 'MM/DD/YYYY') END AS startdate,
		       CASE WHEN ts.tsrefid > 0 THEN TO_CHAR(stdexitdt, 'MM/DD/YYYY') END AS enddate,
		       CASE WHEN ts.tsrefid > 0 THEN fdc.dccode END AS fdccode,
		       CASE WHEN ts.tsrefid > 0 THEN edc.dccode END AS edccode
		  FROM webset.dmg_studentmst std
               LEFT JOIN tmp_teacherstudentassignment AS tmp ON tmp.stdrefid = std.stdrefid
		       LEFT JOIN  webset.sys_teacherstudentassignment AS ts ON ts.stdrefid = tmp.stdrefid AND " . ENTERDATE . " = tmp.dt
               LEFT JOIN (
		            SELECT DISTINCT ON (dm.stdrefid) dm.stdrefid, dccode
		              FROM webset.std_disabilitymst AS dm
					 INNER JOIN webset.statedef_disablingcondition dc ON dc.dcrefid = dm.dcrefid
					 WHERE dm.sdtype = 1
			   ) AS fdc ON (fdc.stdrefid = ts.tsrefid)
               LEFT JOIN (
		            SELECT DISTINCT ON (dm.stdrefid) dm.stdrefid, dccode
		              FROM webset.std_disabilitymst AS dm
					 INNER JOIN webset.statedef_disablingcondition dc ON dc.dcrefid = dm.dcrefid
					 WHERE dm.sdtype > 1
			   ) AS edc ON (edc.stdrefid = ts.tsrefid)
		 WHERE std.vndrefid = 180
		   AND COALESCE(stdstatus, 'A') = 'A'
		 ORDER BY upper(stdLNM), upper(stdFNM);
	")->assocAll();

	$rcdoc = new RCDocument(RCPageFormat::A4, RCDocumentFormat::CSV_COMMA_SEPARATED);

	$layout = RCTable::factory();
	$layout
		->addColumn('student.ext2ID')
		->addColumn('student.spedStatus')
		->addColumn('student.spedStartDate')
		->addColumn('student.spedEndDate')
		->addColumn('student.primaryExceptionality')
		->addColumn('student.secondaryExceptionality');

	foreach ($data as $item) {
		$layout
			->addRow()
			->addCell($item['stdschid'])
			->addCell($item['spedstatus'])
			->addCell($item['startdate'])
			->addCell($item['enddate'])
			->addCell($item['fdccode'])
			->addCell($item['edccode']);
	}

	$rcdoc->addObject($layout);
	SystemCore::$FS->makeDir('/sec_disk_root/SPEDEX/Reports');
	SystemCore::$FS->copy($rcdoc->compile(), '/sec_disk_root/SPEDEX/Reports/sped_enrol_report.csv');


?>
