<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->title = "Add/Edit Form Category";

	$edit->setSourceTable('webset.def_formcategories', 'mfccatrefid');

	$edit->addGroup("General Information");

	$edit->addControl("Form Category")
		->sqlField('mfccatdesc')
		->req();

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./form_category_list.php');
	$edit->cancelURL = CoreUtils::getURL('./form_category_list.php');

	$edit->printEdit();
?>
