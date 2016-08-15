<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$SQL        = "
		INSERT INTO webset_tx.std_sat_language (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_sat_language
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear)
        ";

	db::execSQL($SQL);

	$RefID = db::execSQL("
		SELECT lrefid
		  FROM webset_tx.std_sat_language
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->title       = "Language";
	$edit->saveAndEdit = true;
	$edit->cancelURL   = "04_language.php";

	$edit->setSourceTable('webset_tx.std_sat_language', 'lrefid');
	$edit->addGroup("General Information");
	$edit->addControl("Date of Home Language Survey", "date")->sqlField('survey');
	$edit->addControl("Results", "edit")->sqlField('resulats')->size(80);
	$edit->addControl(FFSwitchYN::factory("Second Language Learning"))->sqlField('secondlang');
	$edit->addControl(FFSwitchYN::factory("Cultural Differences"))->sqlField('cultural');
	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>