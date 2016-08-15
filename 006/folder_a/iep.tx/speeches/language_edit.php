<?php

	Security::init();

	$RefID      = io::geti('RefID');
	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_lang_scores', 'refid');

	$edit->title = "Add/Edit Language Assessment Scores";

	$edit->addGroup("General Information");
	$edit->addControl("Test", "textarea")
		->sqlField('test')
		->css("WIDTH", "100%")
		->css("HEIGHT", "50px")
		->req();

	$edit->addControl("Standard Score:", "edit")
		->sqlField('score')
		->size(20)
		->req();

	$edit->addControl("Percentile Rank:", "edit")
		->sqlField('rank')
		->size(20)
		->req();

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