<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$list->title = "Add/Edit Valid Values";

	$edit->setSourceTable('webset.glb_validvalues', 'refid');

	$edit->addGroup("General Information");

	$edit->addControl("Value Text", "TEXTAREA")->sqlField('validvalue');
	$edit->addControl("Value Code (if needed)", "EDIT")->sqlField('validvalueid');
	$edit->addControl("Sequence Number", "INTEGER")->sqlField('sequence_number');
	$edit->addControl("Expire Date", "DATE")->sqlField('glb_enddate');
	$edit->addUpdateInformation();

	$edit->cancelURL = CoreUtils::getURL('./validvalues_list.php', array('area' => io::get('area')));
	$edit->finishURL = CoreUtils::getURL('./validvalues_list.php', array('area' => io::get('area')));

	$edit->printEdit();
?>
