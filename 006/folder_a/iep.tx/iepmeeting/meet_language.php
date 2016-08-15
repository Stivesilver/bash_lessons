<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = IDEAStudentTX::factory($tsRefID);

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Meeting Language';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '70%';

	$edit->setSourceTable('webset_tx.std_meet_language', 'iepyear');

	$edit->addGroup('Teacher Information');
	$edit->addControl('Teacher')
		->sqlField('teachername')
		->value($student->get('cmname'));

	$edit->addGroup('General Information');
	$edit->addControl('The student\'s dominant language is')
		->sqlField('dominant_language')
		->value($student->get('prim_lang'));

	$edit->addControl(
		FFSwitchYN::factory('An interpreter was used to assist in conducting the meeting')
			->sqlField('interpreter_used')
			->emptyOption(TRUE)
			->breakRow()
	);

	$edit->addControl('If Yes, specify the language or other mode of communication')
		->sqlField('interpreter_mode');

	$edit->addControl(
		FFSwitchYN::factory('A written translation of the IEP in the parent\'s/adult student\'s dominant language was given to the parent/adult student')
			->sqlField('writing_translate')
			->emptyOption(TRUE)
			->breakRow()
	);

	$edit->addControl(
		FFSwitchYN::factory('An audio tape translation of the IEP in the parent\'s/adult student\'s dominant language was given to the parent/adult student.')
			->sqlField('audio_tape')
			->emptyOption(TRUE)
			->breakRow()
	);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_meet_language')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>