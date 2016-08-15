<?php
	Security::init();
	$set_ini = IDEAFormat::getIniOptions();
	
    $dskey   = io::get('dskey');
	$tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
	
	$edit = new EditClass('edit1', io::geti('RefID'));
		
    $edit->title = 'Add/Edit ' . $set_ini['iep_additional_info_title'];

	$edit->setSourceTable('webset.std_additionalinfo', 'siairefid');

	$edit->finishURL = CoreUtils::getURL('srv_addinfo.php', array('dskey'=>$dskey)); 
	$edit->cancelURL = CoreUtils::getURL('srv_addinfo.php', array('dskey'=>$dskey)); 

	$edit->addGroup('General Information');
	$edit->addControl($set_ini['iep_additional_info_title'], 'textarea')
		->sqlField('siaitext')
		->css('width', '100%')
		->css('height', '200px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');		
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');		
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->printEdit();

?>