<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudentCT($tsRefID);
	$nexttab = io::geti('nexttab');

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'State and District Testing and Accommodations';

	$edit->saveLocal = false;
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->firstCellWidth = '40%';

	$data = $student->getTesting(2);

	$edit->addGroup('Assessment Options');

	$edit->addControl('This student has (circle one)', 'select_radio')
		->value(isset($data['has_student']) ? $data['has_student'] : '')
		->name('has_student')
		->sql(IDEADef::getValidValueSql("CT_SBAC_Options", "refid, validvalue"))
		->breakRow();

	$edit->addControl(
		FFSwitchYN::factory('This is an English Learner - EL (circle one)')
			->name('has_learner')
			->value(isset($data['has_learner']) ? $data['has_learner'] : '')
	);

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_assess_state')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);


	$edit->setPresaveCallback('saveData', 'testing_edit.inc.php', array('mode' => 2, 'stdrefid' => $tsRefID, 'iepyear' => $stdIEPYear));
	$edit->finishURL = 'javascript:parent.switchTab(' . $nexttab . ')';
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>
