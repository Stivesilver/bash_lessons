<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.sped_constructions_group', 'cgrefid');

	$edit->title = "Add/Edit Constructions Group";

	$edit->addGroup('General Information');

	$edit->addControl("Name")->sqlField('cgname')->width(300);
	$edit->addControl("Deactivation Date", "date")->sqlField('enddate');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./constructions_group_list.php', array('staterefid' => -1));
	$edit->cancelURL = CoreUtils::getURL('./constructions_group_list.php', array('staterefid' => -1));

	$edit->printEdit();
?>
