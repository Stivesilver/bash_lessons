<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_sat_speech (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_sat_speech
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear
                           )
        ";

	db::execSQL($SQL);

	$RefID = db::execSQL("
		SELECT sprefid
	 	  FROM webset_tx.std_sat_speech
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_sat_speech', 'sprefid');

	$edit->title          = "Speech/Language/Communication";
	$edit->saveAndEdit    = true;
	$edit->firstCellWidth = "30%";

	$edit->addGroup("General Information");
	$edit->addControl(FFSwitchYN::factory("There are concerns for speech/language/communication"))->sqlField('concerns');
	$edit->addGroup("Articulation");
	$edit->addControl(FFSwitchYN::factory("Articulation concern:"))->sqlField('articulation_sw');

	$edit->addControl("Describe Articulation concern:", "textarea")
		->sqlField('articulation')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Fluency");
	$edit->addControl(FFSwitchYN::factory("Fluency concern:"))->sqlField('fluency_sw');

	$edit->addControl("Describe Fluency concern:", "textarea")
		->sqlField('fluency')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Language");
	$edit->addControl(
			FFSwitchYN::factory("Language concern:")
		)
		->sqlField('language_sw');

	$edit->addControl("Describe Language concern:", "textarea")
		->sqlField('language')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Voice");
	$edit->addControl(
			FFSwitchYN::factory("Voice concern:")
		)
		->sqlField('voice_sw');

	$edit->addControl("Describe Voice concern:", "textarea")
		->sqlField('voice')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>