<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_speech_general (stdrefid, iepyear)
	    SELECT $tsRefID, $stdIEPYear
	     WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_speech_general
	                        WHERE stdrefid = $tsRefID
	                          AND iepyear = $stdIEPYear)
	    ";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_speech_general
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")
	->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_general', 'refid');

	$edit->title = "Language/Communicative Status";

	$edit->addGroup("General Information");
	$edit->addControl(FFSwitchYN::factory("This student has a communication disorder"))->sqlField('disorder');

	$edit->addControl("Reason for Referral to Special Education", "textarea")
		->sqlField('rfr')
		->css("WIDTH", "100%")
		->css("HEIGHT", "70px");

	$edit->addControl("Educational History", "textarea")
		->sqlField('eduhistory')
		->css("WIDTH", "100%")
		->css("HEIGHT", "70px");

	$edit->addControl("General Observations", "textarea")
		->sqlField('observations')
		->css("WIDTH", "100%")
		->css("HEIGHT", "70px");

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->saveAndEdit    = true;
	$edit->saveAndAdd     = false;
	$edit->firstCellWidth = "30%";
	$edit->finishURL  = 'javascript:parent.parent.selectNext()';

	$edit->printEdit();

?>