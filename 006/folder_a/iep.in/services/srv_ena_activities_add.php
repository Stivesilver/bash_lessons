<?php
	Security::init();
	
    $dskey   = io::get('dskey');
	$tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
	
	$edit = new EditClass('edit1', io::geti('RefID'));
		
    $edit->title = 'Add/Edit Student Extracurricular and Non-Academic Activity';

	$edit->setSourceTable('webset.std_in_ena_activities', 'siearefid');

	$edit->finishURL = CoreUtils::getURL('srv_ena_activities.php', array('dskey'=>$dskey)); 
	$edit->cancelURL = CoreUtils::getURL('srv_ena_activities.php', array('dskey'=>$dskey)); 

	$edit->addGroup('General Information');
	$edit->addControl('Narrative', 'textarea')
		->sqlField('sieanarrtext')
		->css('width', '100%')
		->css('height', '200px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');		
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');		
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->printEdit();

?>