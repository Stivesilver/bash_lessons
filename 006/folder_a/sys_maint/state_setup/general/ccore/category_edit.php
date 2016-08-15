<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit District Control Option';

	$edit->setSourceTable('webset.statedef_ccore_cat', 'catrefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory())
		->caption('Subject')
		->sqlField('srefid')
		->sql('
			SELECT srefid, name
			  FROM webset.statedef_ccore_subj
		');
	$edit->addControl('Name')->sqlField('name');
	$edit->addControl('Description')->sqlField('description');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->printEdit();

?>
