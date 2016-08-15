<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_sat_airetained (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_sat_airetained
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear)
        ";

	db::execSQL($SQL);

	$RefID = db::execSQL("
		SELECT rrefid
		  FROM webset_tx.std_sat_airetained
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_sat_airetained', 'rrefid');

	$edit->title       = "Academic Information";
	$edit->saveAndEdit = true;
	$edit->cancelURL   = "04_academic_retained_edit.php";

	$edit->addGroup("General Information");
	$edit->addControl(FFSwitchYN::factory("Has the student been retained"))->sqlField('retained');
	$edit->addControl("If yes, when and what was the basis", "textarea")
		->sqlField('basis')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>