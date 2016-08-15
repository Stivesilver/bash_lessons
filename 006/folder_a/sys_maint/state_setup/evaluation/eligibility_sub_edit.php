<?php
	Security::init();

	$edit = new editClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset.es_statedef_eligibility_sub', 'elsrefid');

	$edit->title = "Add/Edit Eligibility Sub Criteria";

	$edit->addGroup("General Information");
	$edit->addControl("Eligibility Criteria", "textarea")->sqlField('elsdesc');
	$edit->addControl("Sequence", "int")->sqlField('seq_num');
	$edit->addControl("Deactivation Date", "date")->sqlField('recdeactivationdt');
	$edit->addControl("Eligibility ID", "hidden")
		->sqlField('elrefid')
		->value(io::get("elrefid"));

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./eligibility_sub_list.php', array('elrefid' => io::get("elrefid")));
	$edit->cancelURL = CoreUtils::getURL('./eligibility_sub_list.php', array('elrefid' => io::get("elrefid")));

	$edit->printEdit();
?>

