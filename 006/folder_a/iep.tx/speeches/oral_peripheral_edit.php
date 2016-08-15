<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "INSERT INTO webset_tx.std_speech_general (stdrefid, iepyear)
            SELECT " . io::get("tsRefID") . ", $stdIEPYear
             WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_speech_general
                                WHERE stdrefid = $tsRefID
                                  AND iepyear = $stdIEPYear)";
	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_speech_general
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_general', 'refid');

	$edit->title = "Oral Peripheral";

	$edit->addGroup("General Information");
	$edit->addControl("Oral Peripheral", "textarea")
		->sqlField('oral')
		->css("width", "100%")
		->css("height", "70px");

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