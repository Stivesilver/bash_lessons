<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');

    $edit = new EditClass('edit1', $tsRefID);

    $edit->title = 'PPT meeting';
	$edit->firstCellWidth = '40%';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->finishURL = 'javascript:parent.switchTab(1);';
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');
    $edit->addGroup('General Information');
    $edit->addControl('The next projected PPT meeting date is', 'date')
        ->sqlField('trs_iepmeetingdt')
        ->name('trs_iepmeetingdt');

	$edit->addControl(FFSwitchYN::factory('Eligible as a student in need of Special Education'))
        ->sqlField('ks_cur_iep')
        ->name('ks_cur_iep')
		->help('The child is evaluated as having a disability, and needs special education and related services');

	$edit->addControl(FFSwitchYN::factory('Is this an amendment to a current IEP using Form ED634?'))
        ->sqlField('ks_trs_iep')
        ->name('ks_trs_iep');

	$edit->addControl('If YES, what is the date of the IEP being amended?', 'date')
		->sqlField('amendment')
		->name('amendment');

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.sys_teacherstudentassignment')
            ->setKeyField('tsrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?>
