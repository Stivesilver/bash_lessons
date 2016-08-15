<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit District Control Option';

	$edit->setSourceTable('webset.statedef_ccore_items', 'itrefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory())
		->caption('Sub Category')
		->sqlField('subrefid')
		->sql('
			SELECT subrefid, name
			  FROM webset.statedef_ccore_subcat
		');

	$edit->addControl('Code')->sqlField('code');
	$edit->addControl('Name')->sqlField('name');
	$edit->addControl('Description')->sqlField('description');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	$edit->printEdit();

?>
