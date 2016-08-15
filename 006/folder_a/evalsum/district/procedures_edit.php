<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$list->title = 'Add/Edit Procedures/Assessments';

	$edit->setSourceTable('webset.es_scr_disdef_proc', 'hsprefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFIDEAEvalScreenType::factory())
		->name('screenid')
		->sqlField('screenid')
		->req(); 
	$edit->addControl('Form Title')->sqlField('hspdesc')->size('50');

	$edit->addControl('XML Template', 'textarea')
		->sqlField('xml_test')
		->value('<doc>' . PHP_EOL . '<line><section font="Courier"><field name="big_text" width="100%" height="500"></field></section></line>' . PHP_EOL . '</doc>');

	$edit->addControl('Deactivation Date', 'date')->sqlField('recdeactivationdt');
	$edit->addUpdateInformation();
	$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

	$edit->cancelURL = './procedures_list.php';
	$edit->finishURL = './procedures_list.php';

	$edit->printEdit();
?>
