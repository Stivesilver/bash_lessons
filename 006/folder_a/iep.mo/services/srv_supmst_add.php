<?php
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	$SQL = "
        SELECT stsrefid
          FROM webset.std_srv_sup                   
         WHERE ssmrefid  = " . $RefID . "
    ";

	$result = db::execSQL($SQL);
	$stsrefid = $result->fields['stsrefid'];

	//Here determine wherever State or District Services we add
	if (($stsrefid == '' and IDEACore::disParam(77) == 'Y') or ($stsrefid == '' and $RefID > 0)) {
		$level = 'district';
		$title = 'District';
		$servId = 'dtsrefid';
	} else {
		$level = 'state';
		$title = 'State';
		$servId = 'stsrefid';
	}

	if ($level == 'district') {
		$servSQL = "
            SELECT NULL, NULL
             UNION ALL
           (SELECT dtsrefid, COALESCE(stscode || ' - ' , '') || stsdesc
              FROM webset.disdef_services_sup
             WHERE vndrefid = VNDREFID
               AND (enddate>now() or enddate is Null)
             ORDER BY CASE lower(stsdesc) WHEN 'other' THEN 'z' ELSE stsdesc END)
        ";
		$naswSQL = "
            SELECT dtsrefid
                   FROM webset.disdef_services_sup
             WHERE vndrefid = VNDREFID
               AND nasw = 'Y'
        ";
		$othSQL = "
            SELECT dtsrefid
              FROM webset.disdef_services_sup
             WHERE vndrefid = VNDREFID
               AND (enddate>now() or enddate is Null)
               AND substring(lower(stsdesc), 1, 5) = 'other'
        ";
	} else {
		$servSQL = "
            SELECT NULL, NULL
             UNION ALL
           (SELECT stsrefid, COALESCE(stscode || ' - ' , '') || stsdesc
              FROM webset.statedef_services_sup
             WHERE screfid = " . VNDState::factory()->id . "
               AND (recdeactivationdt>now() or recdeactivationdt is Null)
             ORDER BY CASE lower(stsdesc) WHEN 'other' THEN 'z' ELSE stsdesc END)
        ";
		$naswSQL = "
            SELECT stsrefid
              FROM webset.statedef_services_sup
             WHERE screfid = " . VNDState::factory()->id . "
               AND nasw = 'Y'
        ";
		$othSQL = "
            SELECT stsrefid
              FROM webset.statedef_services_sup
             WHERE screfid = " . VNDState::factory()->id . "
               AND (recdeactivationdt>now() or recdeactivationdt is Null)
               AND substring(lower(stsdesc), 1, 5) = 'other'
        ";
	}

	$id_oth = db::execSQL($othSQL)->indexAll();
	$id_na = db::execSQL($naswSQL)->indexAll();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Supplementary Aid/Services';

	$edit->setSourceTable('webset.std_srv_sup', 'ssmrefid');

	$edit->addGroup('General Information');
	$edit->addControl($title . ' Service', 'select')
		->sqlField($servId)
		->name($servId)
		->sql($servSQL)
		->req();

	$edit->addControl('Narrative', 'textarea')
		->sqlField('narrative');

	$edit->addControl('Beginning Date', 'date')
		->sqlField('ssmbegdate')
		->value($student->getDate('stdenrolldt'))
		->hideIf($servId, $id_na);

	$edit->addControl('Ending Date', 'date')
		->sqlField('ssmenddate')
		->value($student->getDate('stdcmpltdt'))
		->hideIf($servId, $id_na);

	$edit->addControl('Time', 'integer')
		->sqlField('ssmtime')
		->size(5)
		->hideIf($servId, $id_na);

	$edit->addControl('Amount', 'select')
		->sqlField('ssmamt')
		->sql("
            SELECT sarefid, 
                   sadesc
              FROM webset.def_spedamt
             WHERE vndrefid = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY seqnum, sadesc
        ")
		->hideIf($servId, $id_na);

	$edit->addControl('Frequency', 'select')
		->sqlField('ssmfreq')
		->sql("
            SELECT sfrefid, 
                   sfdesc
              FROM webset.def_spedfreq
             WHERE vndrefid = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY seqnum, sfdesc
        ")
		->hideIf($servId, $id_na);

	$edit->addControl('Location', 'select')
		->sqlField('ssmclasstype')
		->name('ssmclasstype')
		->sql("
            SELECT crtrefid, 
                   crtdesc
              FROM webset.def_classroomtype
             WHERE (recdeactivationdt>now() or recdeactivationdt is Null)
             ORDER BY CASE substring(lower(crtdesc), 1, 5)  WHEN 'other' THEN 'z' ELSE crtdesc END
        ")
		->hideIf($servId, $id_na)
		->value(2);

	$edit->addControl('Specify')
		->sqlField('ssmclasstypenarr')
		->showIf('ssmclasstype', db::execSQL("
                                  SELECT crtrefid
                                    FROM webset.def_classroomtype
                                   WHERE substring(lower(crtdesc), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Implementor', 'select')
		->sqlField('umrefid')
		->name('umrefid')
		->sql("
			SELECT NULL, NULL
			 UNION ALL
		   (SELECT umrefid,  umlastname || ', ' || umfirstname
			  FROM sys_usermst
			 WHERE vndrefid = VNDREFID                       
			   AND COALESCE(um_internal, true)
			 ORDER BY 2)
		")
		->hideIf($servId, $id_na);

	$edit->addControl('Other Implementor')
		->sqlField('impl_oth')
		->size(50);


	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('srv_supmst.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_supmst.php', array('dskey' => $dskey));

	$edit->printEdit();


?>
