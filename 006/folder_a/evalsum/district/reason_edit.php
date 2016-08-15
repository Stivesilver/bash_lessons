<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$list->title = 'Add/Edit Reason for Evaluation';

	$edit->setSourceTable('webset.es_disdef_eval_reason', 'rrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Reason')->sqlField('rdesc')->size('50');
	$edit->addControl('Deactivation Date', 'date')->sqlField('recdeactivationdt');
	$edit->addUpdateInformation();
	$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

	$edit->cancelURL = './referral_list.php';
	$edit->finishURL = './referral_list.php';

	$edit->printEdit();
?>
