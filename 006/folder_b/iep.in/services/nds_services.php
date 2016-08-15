<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $tsRefID);
	
	$edit->title = 'Edit Non District Support Services';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	
	$edit->setSourceTable('webset.std_in_nondistrict_services', 'stdrefid');

	$edit->addGroup('General Information');
	$edit->addControl('List any Non District support services such as CASA, counseling, caseworker, probation officer, advocate, etc:', 'textarea')
		->sqlField('sinstext')
		->css('width', '100%')
		->css('height', '150px');

	$edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        

    $edit->finishURL = CoreUtils::getURL($screenURL, array('dskey'=>$dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey'=>$dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_nondistrict_services')
			->setKeyField('stdrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>