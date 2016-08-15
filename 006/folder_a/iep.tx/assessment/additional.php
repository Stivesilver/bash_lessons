<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", 0);

	$edit->title = 'Review of Assessment Data';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->addGroup('General Information');

	$edit->addControl('The ARD/IEP committee addressed the need for additional assessment and determined', 'select_radio')
		->name('need')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'assessment_additional_needed', $stdIEPYear))
		->data(
			array(
				'N' => 'No additional data is needed because the existing data is appropriate to determine whether the student has or
				 	  continues to have a disability under IDEA and an educational need for special education and related services.
				  	  The existing data is sufficient to develop an appropriate IEP for the student.
					  (If this refers to the discussion of reevaluation, see attached Determination of Needed Evaluation.)',
				'Y' => 'Additional data is needed to determine whether any additions or modifications to the student\'s special education
                      program are needed to enable the student to achieve the measurable annual goals and objectives and to participate
                      as appropriate in the general curriculum. If checked, identify the type of evaluation and the date it will be completed'
			)
	);

	$edit->addControl('Identify the type of evaluation and the date it will be completed', 'textarea')
		->name('evaltype')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'assessment_additional_evaluation', $stdIEPYear))
		->css('width', '100%')
		->css('height', '50px')
		->showIf('need', 'Y');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));	
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->saveURL = CoreUtils::getURL('additional_save.php', array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_dates')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>