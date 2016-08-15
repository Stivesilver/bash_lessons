<?php
	Security::init();

	$dskey     = io::get('dskey');
	$RefID     = io::geti('RefID');
	$ds        = DataStorage::factory($dskey);
	$tsRefID   = $ds->safeGet('tsRefID');
	$student   = new IDEAStudent($tsRefID);

	$SQL = "
        SELECT stsrefid
          FROM webset.std_srv_rel
         WHERE ssmrefid  = ".$RefID."
    ";

	$result = db::execSQL($SQL);
	$stsrefid = $result->fields['stsrefid'];

	//Here determine wherever State or District Services we add
	if (($stsrefid=='' and IDEACore::disParam(77)=='Y') or ($stsrefid=='' and $RefID>0)) {
		$level  = 'district';
		$title  = 'District';
		$servId = 'dtrrefid';
	} else {
		$level  = 'state';
		$title  = 'State';
		$servId = 'stsrefid';
	}

	if ($level == 'district') {
		$servSQL = "
            SELECT NULL, NULL
             UNION ALL
           (SELECT dtrrefid, COALESCE(strcode || ' - ' , '') || strdesc
              FROM webset.disdef_services_rel
             WHERE vndrefid = VNDREFID
               AND (enddate>now() or enddate is Null)
             ORDER BY CASE lower(strdesc) WHEN 'other' THEN 'z' ELSE strdesc END)
        ";
		$naswSQL = "
            SELECT dtrrefid
                   FROM webset.disdef_services_rel
             WHERE vndrefid = VNDREFID
               AND nasw = 'Y'
        ";
	} else {
		$servSQL = "
            SELECT NULL, NULL
             UNION ALL
           (SELECT strrefid, COALESCE(strcode || ' - ' , '') || strdesc
              FROM webset.statedef_services_rel
             WHERE screfid = ".VNDState::factory()->id."
			   AND COALESCE(onlythisvnd,'" . SystemCore::$VndName . "') like '%" . SystemCore::$VndName . "%'
               AND (recdeactivationdt>now() or recdeactivationdt is Null)
             ORDER BY CASE lower(strdesc) WHEN 'other' THEN 'z' ELSE strdesc END)
        ";
		$naswSQL = "
            SELECT strrefid
              FROM webset.statedef_services_rel
             WHERE screfid = ".VNDState::factory()->id."
               AND nasw = 'Y'
        ";
	}

	$id_na  = db::execSQL($naswSQL)->indexAll();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Special Education Services';

	$edit->setSourceTable('webset.std_srv_rel', 'ssmrefid');

	$edit->addGroup('General Information');
	$edit->addControl($title . ' Service', 'select')
		->sqlField($servId)
		->name($servId)
		->sql($servSQL)
		->req();

	$edit->addControl('Beginning Date', 'date')
		->sqlField('ssmbegdate')
		->value($student->getDate('stdenrolldt'))
		->hideIf($servId, $id_na);

	$edit->addControl('Ending Date', 'date')
		->sqlField('ssmenddate')
		->value($student->getDate('stdcmpltdt'))
		->hideIf($servId, $id_na);

	$edit->addGroup('Frequency/Duration');
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

	$edit->addGroup('Location');
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
		->hideIf($servId, $id_na);

	$edit->addControl('Specify')
		->sqlField('ssmclasstypenarr')
		->showIf('ssmclasstype', db::execSQL("
                                  SELECT crtrefid
                                    FROM webset.def_classroomtype
                                   WHERE substring(lower(crtdesc), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	if (IDEACore::disParam(106) != 'N') {
		$edit->addControl(FFUserSearch::factory())
			->caption('Service Implementor')
			->sqlField('umrefid')
			->name('umrefid')
			->hideIf($servId, $id_na);
	}

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('srv_relmst.php', array('dskey'=>$dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_relmst.php', array('dskey'=>$dskey));

	$edit->printEdit();


?>
