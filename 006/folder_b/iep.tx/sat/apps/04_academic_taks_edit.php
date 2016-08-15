<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset_tx.std_sat_aitaks', 'trefid');

	$edit->title = "Add/Edit Texas Assessment of Knowledge and Skills";

	$edit->addGroup("General Information");
	$edit->addControl("Date:", "date")->sqlField('tdate');
	$edit->addControl("Subject", "edit")
		->sqlField('subject')
		->req(true)
		->size(50);

	$edit->addControl(FFSwitchYN::factory("Total Test Mastery:"))->sqlField('mastery');
	$edit->addControl("Scaled Score:", "edit")
		->sqlField('score')
		->size(5);

	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->finishURL = CoreUtils::getURL('04_academic_taks_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('04_academic_taks_list.php', array('dskey' => $dskey));
	$edit->printEdit();

?>