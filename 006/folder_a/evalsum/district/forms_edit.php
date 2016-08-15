<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$list->title = 'Add/Edit Form';

	$edit->setSourceTable('webset.es_disdef_evalforms', 'efrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Form Title')->sqlField('form_title')->size('50');
	$edit->addControl('Deactivation Date', 'date')->sqlField('recdeactivationdt');
	$edit->addUpdateInformation();
	$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

	$edit->cancelURL = './forms_list.php';
	$edit->finishURL = './forms_list.php';

	$edit->printEdit();
?>
