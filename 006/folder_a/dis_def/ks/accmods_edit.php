<?php
	Security::init();

	$edit = new editClass('edit1', io::geti('RefID'));

	$edit->title = 'Add/Edit Assessment Accommodations';

	$edit->setSourceTable('webset.statedef_mod_acc', 'stsrefid');
	$edit->firstCellWidth = '30%';

	$edit->addGroup('General Information');
	$edit->addControl('Area', 'select_radio')
		->sqlField('aacrefid')
		->sql("
			SELECT aacrefid,
			       aacdesc
			  FROM webset.statedef_assess_acc_cat
			 WHERE screfid = " . VNDState::factory()->id . "
			 ORDER BY aacdesc
        ")
		->breakRow()
		->req();

	$edit->addControl('Assessment Accommodation')
		->sqlField('stsdesc')
		->css("width", "80%")
		->req();

	$edit->addControl('Deactivation Date', 'date')->sqlField('recdeactivationdt');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('State ID', 'hidden')->value(VNDState::factory()->id)->sqlField('screfid');
	$edit->addControl('ProgMod Flag', 'hidden')->value('Y')->sqlField('assessmentsw');

	$edit->finishURL = "accmods_list.php";
	$edit->cancelURL = "accmods_list.php";

	$edit->printEdit();

?>