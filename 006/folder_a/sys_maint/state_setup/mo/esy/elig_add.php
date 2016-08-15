<?php
	Security::init();

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.glb_validvalues', 'refid');

	$edit->title = "Add/Edit Eligibilty for ESY services";

	$edit->addGroup("General Information");

	$edit->addControl("Value", "EDIT")->sqlField('validvalueid');
	$edit->addControl("Description", "EDIT")->sqlField('valuename');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./elig_list.php');
	$edit->cancelURL = CoreUtils::getURL('./elig_list.php');

	$edit->printEdit();
?>
