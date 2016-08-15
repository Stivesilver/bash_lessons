<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.sped_ini', 'irefid');

	$edit->title = "Add/Edit Initialisation Option";

	$edit->addGroup('General Information');

	$edit->addControl("Option Name")
		->sqlField('ini_name')
		->req();

	$edit->addControl("Code Word")
		->sqlField('ini_codeword')
		->req();

	$edit->addControl("Description", "textarea")
		->sqlField('ini_desc');

	$edit->addControl("Default Value", "textarea")
		->sqlField('ini_default');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./inidef_list.php', array('staterefid' => -1));
	$edit->cancelURL = CoreUtils::getURL('./inidef_list.php', array('staterefid' => -1));

	$edit->printEdit();
?>
