<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Transition Services';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '0%';

	$edit->setSourceTable('webset_tx.std_trans_serv', 'iep_year');

	$edit->addGroup('General Information');

	$edit->addControl('', 'select_check')
		->sqlField('dt_age')
		->data(array('Y' => 'N/A due to age'))
		->displaySelectAllButton(FALSE);
	
	$edit->addControl('', 'select_check')
		->sqlField('age14')
		->data(array('Y' => 'Beginning at age 14 <i>(or younger if deemed appropriate by the IEP team)</i> the IEP
                                               must include a statement of the transition service needs of the student under the applicable
                                               components in the student\'s courses of study. The statement is updated annually.'))
		->displaySelectAllButton(FALSE);

	$edit->addControl('', 'select_check')
		->sqlField('career_c')
		->name('career_c')
		->data(array('Y' => 'The student is interested in pursuing the following career:'))
		->displaySelectAllButton(FALSE);
	
	$edit->addControl('')
		->sqlField('career_t')
		->size('45')
		->showIf('career_c' , 'Y');
	
	$edit->addControl('', 'select_check')
		->sqlField('courses_c')
		->name('courses_c')
		->data(array('Y' => 'Courses to consider:'))
		->displaySelectAllButton(FALSE);
	
	$edit->addControl('')
		->sqlField('courses_t')
		->size('45')
		->showIf('courses_c' , 'Y');
	
	$edit->addControl('', 'select_check')
		->sqlField('notice17')
		->data(array('Y' => 'Notice of transfer of parental rights required on or before the student\'s 17th birthday'))
		->displaySelectAllButton(FALSE);
	
	$edit->addControl('', 'select_check')
		->sqlField('inform17')
		->data(array('Y' => 'The student/parent has been informed, on or before the student\'s 17th birthday, that the rights granted to parents
                                               under IDEA will transfer to the student when he/she reaches the age of 18 unless the parent has obtained
                                               guardianship.  The parent will continue to receive notice of ARD/IEP committee meetings'))
		->displaySelectAllButton(FALSE);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('std_refid');

	$edit->finishURL = 'javascript:parent.switchTab();';
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sam_other')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>