<?php
	Security::init();

	$edit = new editClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset.es_statedef_screeningtype', 'scrrefid');

	$edit->title = "Add/Edit Screening Types";

	$edit->addGroup("General Information");

	$edit->addControl("Sequence #", 'int')->sqlField('scrseq');
	$edit->addControl("Screening Type", 'text')->sqlField('scrdesc');
	$edit->addControl("Desc", 'textarea')->sqlField('scrlongdesc')->autoHeight(true);

	$edit->addControl("State ID", "hidden")
		->sqlField('screfid')
		->value(io::get("staterefid"));

	$edit->addControl("Deactivation Date", "date")->sqlField('enddate');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./scr_type_list.php', array('staterefid' => io::get("staterefid")));
	$edit->cancelURL = CoreUtils::getURL('./scr_type_list.php', array('staterefid' => io::get("staterefid")));

	$edit->printEdit();
?>

