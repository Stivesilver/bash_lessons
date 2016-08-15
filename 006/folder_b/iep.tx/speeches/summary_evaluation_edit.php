<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_speech_summary (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_speech_summary
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear
                      	   )
        ";
	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_speech_summary
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_summary', 'refid');

	$edit->title = "Summary of Evaluation";

	$edit->addGroup("General Information");
	$edit->addControl(FFSwitchYN::factory("Is a speech-language disorder present? "))->sqlField('disorder_sw');

	$edit->addControl("Comments", "textarea")
		->sqlField('disorder_txt')
		->css("width", "100%")
		->css("height", "70px");

	$edit->addControl(FFSwitchYN::factory("Is there an adverse effect on education?"))->sqlField('adverse_sw');

	$edit->addControl("Comments", "textarea")
		->sqlField('adverse_txt')
		->css("width", "100%")
		->css("height", "70px");

	$edit->addControl(FFSwitchYN::factory("Are speech pathology services needed? "))->sqlField('pathology_sw');

	$edit->addControl("Comments", "textarea")
		->sqlField('pathology_txt')
		->css("width", "100%")
		->css("height", "70px");

	$edit->addControl(FFSwitchYN::factory("Does the student meet the criteria as speech impaired?"))->sqlField('criteria_sw');

	$edit->addControl("Comments", "textarea")
		->sqlField('criteria_txt')
		->css("width", "100%")
		->css("height", "70px");

	$edit->addGroup("Summary Information");
	$edit->addControl("Evaluation Summary", "textarea")
		->sqlField('eval_summary')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->saveAndEdit    = true;
	$edit->saveAndAdd     = false;
	$edit->firstCellWidth = "30%";
	$edit->finishURL      = 'javascript:parent.parent.selectNext()';

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>