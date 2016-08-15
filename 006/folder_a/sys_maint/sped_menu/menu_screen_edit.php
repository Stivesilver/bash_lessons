<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass("edit1", $RefID);

	$edit->setSourceTable('webset.sped_screen', 'scr_refid');

	$edit->title = "Add/Edit Screen Type";

	$edit->addGroup('General Information');
	$edit->addControl("Code Word")->sqlField('scr_codeword')->req();
	$edit->addControl("Screen Type")->sqlField('scr_name')->width(300)->req();
	$edit->addControl("Screen URL")->sqlField('scr_url')->width(300)->req();
	$edit->addControl(FFSwitchYN::factory("Default Screen"))->sqlField('scr_default_sw');
	$edit->addControl("Screen Description", "textarea")->sqlField('scr_desc');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./menu_screen_list.php');
	$edit->cancelURL = CoreUtils::getURL('./menu_screen_list.php');

	$edit->printEdit();
?>
