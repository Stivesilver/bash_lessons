<?php
	Security::init();
	
    $dskey   = io::get('dskey');
	$tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
	
	$edit = new EditClass('edit1', io::geti('RefID'));
		
    $edit->title = 'Add/Edit Specific Behavior Goal';

	$edit->setSourceTable('webset.std_in_bipgoals', 'grefid');

	$edit->finishURL = CoreUtils::getURL('goals.php', array('dskey'=>$dskey)); 
	$edit->cancelURL = CoreUtils::getURL('goals.php', array('dskey'=>$dskey)); 

	$edit->addGroup('General Information');
	$edit->addControl('Goal', 'textarea')
		->sqlField('goal')
		->css('width', '100%')
		->css('height', '100px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');		
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');		
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->printEdit();

?>