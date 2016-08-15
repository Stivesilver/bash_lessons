<?php

	Security::init();
	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new EditClass("edit1", $samrefid);

	$edit->title = 'TPRI/TEJAS LEE(K, 1, 2)';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;

	$edit->setSourceTable('webset_tx.std_sam_general', 'samrefid');

	$edit->addGroup('General Information');

	$edit->addControl(
		FFSwitchYN::factory('The student will take the TPRI/TEJAS LEE')
			->sqlField('trpi_take')
			->data(array('Y' => 'Yes', 'N' => 'No', 'A' => 'N/A'))
	);

	$edit->addControl('If no, identify the local alternative assessment', 'textarea')
		->sqlField('trpi_alternative')
		->css('width', '100%')
		->css('height', '100px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = 'javascript:api.window.destroy();';
	$edit->cancelURL = 'javascript:api.window.destroy();';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sam_general')
			->setKeyField('samrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>