<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.def_spedstatus', 'stsrefid');

	$edit->title = "Add/Edit Special Education Student Status";

	$edit->addGroup("General Information");

	$edit->addControl("Status")->sqlField('stsdesc');

	$edit->addControl(FFSwitchYN::factory("Special Education Active"))
		->sqlField('active_sw');

	$edit->addControl(FFSwitchYN::factory("Special Demographics Active"))
		->sqlField('stddmg_active_sw');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./spedstatus_list.php');
	$edit->cancelURL = CoreUtils::getURL('./spedstatus_list.php');

	$edit->printEdit();
?>
