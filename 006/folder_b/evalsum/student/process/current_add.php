<?php

	Security::init();

	$dskey = io::get('dskey');
	$tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
	$eval_process = db::execSQL("
		SELECT essrtdescription as eval_type,
		       date_start
		  FROM webset.es_std_evalproc AS ep
		       INNER JOIN webset.es_statedef_reporttype rt ON rt.essrtrefid = ep.ev_type
		 WHERE ep.eprefid = " . io::geti('RefID') . "
		")->assoc();
	$edit = new EditClass('edit1', io::geti('RefID'));

	$edit->title = 'Select Evaluation Process';

	$edit->setSourceTable('webset.es_std_evalproc', 'eprefid');

	$edit->getButton(EditClassButton::SAVE_AND_FINISH)->value('Save Evaluation Process');

	$edit->addGroup('General Information');
	$edit->addControl("Evaluation Start Date", 'protected')
		->value(CoreUtils::formatDateForUser($eval_process['date_start']));
	$edit->addControl("Evaluation Type", 'protected')
		->value($eval_process['eval_type']);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->setPostsaveCallback('postSave', './current_save.inc.php', array('dskey' => $dskey));

	$edit->finishURL = "javascript:parent.api.reload({'eval_set': 'sel'});";
	$edit->cancelURL = CoreUtils::getURL('current_list.php', array('dskey' => $dskey));

	$edit->saveLocal = false;
	$edit->saveAndAdd = false;
	$edit->firstCellWidth = '40%';

	$edit->printEdit();
?>
