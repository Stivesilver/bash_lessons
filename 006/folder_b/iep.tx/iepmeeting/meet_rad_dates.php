<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Review of Assessment Data';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset_tx.std_dates', 'iepyear');

	$edit->addGroup('General Information');

	$edit->addControl('Assistive Technology Assessment', 'date')
		->sqlField('assistive');
	
	$edit->addControl('Functional Behavior Assessment', 'date')
		->sqlField('fba');
	
	$edit->addControl('Functional Vocational Evaluation', 'date')
		->sqlField('fve');
	
	$edit->addControl('Related Services Assessment')
		->sqlField('relatedasm')
		->size(50);
	
	$edit->addControl('Related Services Assessment Date', 'date')
		->sqlField('related');
	
	$edit->addControl('Speech and Language Assessment', 'date')
		->sqlField('speach');
	
	$edit->addControl('Transition Services', 'date')
		->sqlField('transition');
	
	$edit->addControl('Other')
		->sqlField('other_desc')
		->size(50);
	
	$edit->addControl('Other Date', 'date')
		->sqlField('other');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

    $edit->finishURL = 'javascript:parent.switchTab(2);';
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_dates')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>