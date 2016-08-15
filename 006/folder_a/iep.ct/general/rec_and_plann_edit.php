<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudentCT($tsRefID);
	$row = $student->getRecAndPlanning();
	$screenURL = $ds->safeGet('screenURL');


	$edit = new EditClass('edit', $row['fprefid']);

	$edit->title = 'Recommendations and Planning';

	$edit->addGroup('General Information');
	$edit->addControl('List of PPT Recommendations', 'textarea')
		->name('recommendations')
		->value($student->getRecommendations());

	$edit->addControl('Planning and placement team meeting summary (optional)', 'textarea')
		->name('planning')
		->value($student->getPlanning());

	$edit->addControl(
			FFSwitchYN::factory('Parental Notification of the Laws Relating')
		)
		->name('parent_notif')
		->value($student->getParentNotiffy());

	$edit->addControl('Parent Date', 'date')
		->name('parent_date')
		->value($student->getParentDate());

	$edit->addControl('', 'hidden')
		->name('tsRefID')
		->value($tsRefID);

	$edit->addControl('', 'hidden')
		->name('fpRefID')
		->value($row['fprefid']);

	$edit->addControl('', 'hidden')
		->name('stdIEPYear')
		->value($stdIEPYear);

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_assess_state')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);


	$edit->setPresaveCallback('saveData', 'rec_and_plann_edit.inc.php', array('dskey' => $dskey));
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	//	$edit->addButton('Save & Finish', 'saveRec()');
	$edit->addUpdateInformation();
	//	$this->setAjaxLink('iepct', 'saveRec', $this->getNameControles($edit));
	$edit->printEdit();

?>
