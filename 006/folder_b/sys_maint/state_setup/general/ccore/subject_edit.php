<?php
	Security::init();

	$RefID = io::geti('RefID');
	$staterefid = io::geti('staterefid');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit District Control Option';

	$edit->setSourceTable('webset.statedef_ccore_subj', 'srefid');

	$edit->addGroup('General Information');
	$edit->addControl('Name')->sqlField('name');
	$edit->addControl('Description')->sqlField('description');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl(FFInput::factory())
		->caption('State ID')
		->hide(true)
		->sqlField('screfid')
		->value($staterefid);


	$edit->printEdit();

?>
