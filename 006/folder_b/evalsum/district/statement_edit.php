<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$list->title = 'Add/Edit Form Statement';

	$edit->setSourceTable('webset.es_formdisselections', 'ssgirefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFIDEAEvalScreenType::factory())->sqlField('screening_id');
	$edit->addControl('Document', 'select')
		->data(
			array(
				'E' => 'Evaluation Report', 
				'R' => 'Review of Existing Data'
			)
		)
		->sqlField('area')
		->name('area');

	$edit->addControl('Section', 'select')
		->data(
			array(
				'1' => 'Description of Data Reviewed', 
				'2' => 'Summary of Information Gained'
			)
		)
		->sqlField('acategory')
		->emptyOption(true)
		->showIf('area', 'R');

	$edit->addControl('Statement', 'TEXTAREA')->sqlField('ssgitext');
	$edit->addUpdateInformation();
	$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

	$edit->cancelURL = CoreUtils::getURL('./statement_list.php', array('area' => io::get('area')));
	$edit->finishURL = CoreUtils::getURL('./statement_list.php', array('area' => io::get('area')));

	$edit->printEdit();
?>
