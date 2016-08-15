<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Supplementary Services and Program Support';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '45%';

	$edit->setSourceTable('webset_tx.std_srv_suppl', 'iep_year');

	$edit->addGroup('General Information');

	$edit->addControl(
		FFSwitchYN::factory('The student is in need of support in the general education setting')
			->sqlField('gen_edu')
			->name('gen_edu')
	);
		
	$edit->addControl('Supplementary Aids and Services for the Student', 'select_check')
		->sqlField('std_c')
		->displaySelectAllButton(false)
		->data(array('Y'=>''))
		->help('See Section C. "Program Interventions, Accommodations or other Program Modifications"')
		->showIf('gen_edu', 'Y');
	
	$edit->addControl('Other', 'textarea')
		->sqlField('std_oth')
		->css('width', '100%')
		->css('height', '100px')
		->showIf('gen_edu', 'Y');
		
	$edit->addControl('Program Modifications for Support For School Personnel', 'select_check')
		->sqlField('pers_c')
		->displaySelectAllButton(false)
		->data(array('Y'=>''))
		->help('See Section C. "Program Interventions, Accommodations or other Program Modifications"')		
		->showIf('gen_edu', 'Y');
	
	$edit->addControl('Other', 'textarea')
		->sqlField('pers_oth')
		->css('width', '100%')
		->css('height', '100px')
		->showIf('gen_edu', 'Y');
	
	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('std_refid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iep_year');

    $edit->finishURL = 'javascript:parent.switchTab(3);';
	$edit->cancelURL = 'javascript:parent.switchTab();';

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