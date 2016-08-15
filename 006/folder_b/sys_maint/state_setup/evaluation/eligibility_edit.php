<?php
	Security::init();

	$edit = new editClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset.es_statedef_eligibility', 'elrefid');

	$edit->title = "Add/Edit Eligibility Criteria";

	$edit->addGroup("General Information");
	$edit->addControl("Code")->sqlField('elcode');
	$edit->addControl("Eligibility Criteria", "textarea")->sqlField('eldesc');
	$edit->addControl("Sequence", "int")->sqlField('seqnum');
	$edit->addControl("Deactivation Date", "date")->sqlField('recdeactivationdt');
	$edit->addControl("State ID", "hidden")
		->sqlField('screfid')
		->value(io::get("staterefid"));
	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./eligibility_list.php', array('staterefid' => io::get("staterefid")));
	$edit->cancelURL = CoreUtils::getURL('./eligibility_list.php', array('staterefid' => io::get("staterefid")));

	$edit->printEdit();
?>

