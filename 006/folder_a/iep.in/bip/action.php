<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$student = IDEAStudent::factory($tsRefID);

	$bgrefid = (int) db::execSQL("
		SELECT bgrefid
    	  FROM webset.std_in_bipgen
         WHERE stdrefid = " . $tsRefID . "
	")->getOne();

	$edit = new EditClass("edit1", $bgrefid);

	$edit->title = 'Edit Behavior Intervention Plan (General Part)';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;

	$edit->setSourceTable('webset.std_in_bipgen', 'bgrefid');

	$edit->addGroup('General Information');	
	$edit->addControl('Plan of Action', 'textarea')
		->sqlField('planoa')
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
			->setTable('webset.std_in_bipgen')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>