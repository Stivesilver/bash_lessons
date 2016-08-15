<?php
	Security::init();

	$edit = new editClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset.sped_doctype', 'drefid');

	$edit->title = "Add/Edit Doc Types";

	$edit->addGroup("General Information");

	$edit->addControl("Name")->sqlField('doctype');
	$edit->addControl("Description", "textarea")->sqlField('docdesc');
	$edit->addControl("Block Class")->sqlField('block_class');
	$edit->addControl("Preview Gen file if not default")->sqlField('preview_gen_file');
	$edit->addControl(FFSwitchYN::factory("Default Document"))
		->sqlField('defaultdoc');
	$edit->addControl("Sequence", "int")->sqlField('seqnum');
	$edit->addControl("Deactivation Date", "date")->sqlField('enddate');
	$edit->addUpdateInformation();

	$edit->addControl("IEP Format", "HIDDEN")
		->value(io::get("iepformat"))
		->sqlField('setrefid');

	$edit->finishURL = CoreUtils::getURL('./iep_docs_list.php', array('iepformat' => io::get("iepformat")));
	$edit->cancelURL = CoreUtils::getURL('./iep_docs_list.php', array('iepformat' => io::get("iepformat")));

	$edit->printEdit();
?>
