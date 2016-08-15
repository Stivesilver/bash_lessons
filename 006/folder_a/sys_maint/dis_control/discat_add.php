<?php

	Security::init();

	$edit = new editClass('edit1', io::get("RefID"));

	$edit->setSourceTable('webset.statedef_discontrol_cat', 'sdcatrefid');

	$edit->title = "Add/Edit District Control Option";

	$edit->addGroup('General Information');
	$edit->addControl('Name')
		->size(50)
		->sqlField('name');

	$edit->addGroup('Update Information', true);
	$edit->addControl("Last User", "PROTECTED")->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl("Last Update", "PROTECTED")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');

	$edit->finishURL = "discat_list.php";
	$edit->cancelURL = "discat_list.php";

	$edit->printEdit();

?>
