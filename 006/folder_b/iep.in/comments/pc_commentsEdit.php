<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	
	$edit = new EditClass("edit1", $tsRefID);
	
	$edit->title = 'Parent Comments';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	
	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

	$edit->addGroup('General Information');
	$edit->addControl('Comments', 'textarea')
		->sqlField('parcomments')
		->css('width', '100%')
		->css('height', '150px');

	$edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        

    $edit->finishURL = CoreUtils::getURL($screenURL, array('dskey'=>$dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey'=>$dskey));
	
	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>