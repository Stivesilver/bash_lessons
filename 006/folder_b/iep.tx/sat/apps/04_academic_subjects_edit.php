<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', io::get("RefID"));

	$edit->setSourceTable('webset_tx.std_sat_aisubjects', 'lrefid');

	$edit->title     = "Add/Edit Subjects and Current Grades";
	$edit->finishURL = "04_academic_subjects_list.php";
	$edit->cancelURL = "04_academic_subjects_list.php";

	$edit->addGroup("General Information");
	$edit->addControl("Subject", "edit")->sqlField('subject')->size(50);
	$edit->addControl("Score", "edit")->sqlField('score')->size(50);
	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');
	$edit->printEdit();

?>