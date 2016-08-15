<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Review of Assessment Data - Concerns';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset_tx.std_sam_other', 'iepyear');

	$edit->addGroup('General Information');

	$edit->addControl('Issues expressed by the parents concerning the education of their child', 'textarea')
		->sqlField('concerns')
		->css('width', '100%')
		->css('height', '150px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = 'javascript:parent.switchTab();';
	$edit->cancelURL = 'javascript:parent.switchTab();';
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sam_other')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>