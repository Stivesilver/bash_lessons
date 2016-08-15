<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', io::get('RefID'));

	$edit->title     = "Add/Edit Achievement Test Data";
	$edit->finishURL = CoreUtils::getURL('04_academic_achievement_data_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('04_academic_achievement_data_list.php', array('dskey' => $dskey));

	$edit->setSourceTable('webset_tx.std_sat_aidata', 'arefid');
	$edit->addGroup("General Information");
	$edit->addControl("Date:", "date")->sqlField('asdate');
	$edit->addControl("Test Name", "edit")
		->sqlField('testname')
		->req(true)
		->size(50);

	$edit->addControl("Subject Area:", "edit")
		->sqlField('subjarea')
		->size(50);

	$edit->addControl("Score:", "edit")
		->sqlField('score')
		->size(5);

	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');
	$edit->printEdit();
?>