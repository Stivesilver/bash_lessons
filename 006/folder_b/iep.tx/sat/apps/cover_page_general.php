<?php

	Security::init();

	$dskey         = io::get('dskey');
	$ds            = DataStorage::factory($dskey, true);
	$stdSchoolYear = $ds->safeGet('stdIEPYear');
	$tsRefID       = $ds->safeGet('tsRefID');

    $SQL = "
        INSERT INTO webset_tx.std_sat_strength (stdrefid, iepyear)
        SELECT $tsRefID, $stdSchoolYear
         WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_sat_strength
                            WHERE stdrefid = " . io::get("tsRefID") . "
                              AND iepyear = $stdSchoolYear)
        ";
    $result = db::execSQL($SQL);
    if (!$result) se($SQL);

    $RefID = (int) db::execSQL("
        SELECT srefid FROM webset_tx.std_sat_strength
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdSchoolYear
        ")->getOne();

    $edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_sat_strength', 'srefid');

    $edit->title          = "Cover Page";
	$edit->finishURL      = true;
	$edit->saveAndEdit    = true;
	$edit->cancelURL      = 'javascript: parent.api.goto(' . json_encode(CoreUtils::getURL('/apps/idea/iep/desktop/desk_menu.php', array('dskey'   => $dskey))) . ')';
	$edit->firstCellWidth = '40%';
	$edit->onSaveDone     = 'parent.switchTab(1);';

	$edit->addGroup("General Information");
	$edit->addControl("Meeting", "edit")
		->maxlength(400)
		->sqlField('meetingtype')
		->size(70);

	$edit->addControl("I am requesting assistance from the RTI in the area of - Check applicable", "select_check")
		->sqlField('sitareas')
		->breakRow()
		->sql("
			SELECT validvalueid,
                   validvalue
              FROM webset.glb_validvalues
             WHERE ValueName = 'TX_SIT_areas'
             ORDER BY sequence_number
            ");

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdSchoolYear)
		->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

    $edit->printEdit();

?>
