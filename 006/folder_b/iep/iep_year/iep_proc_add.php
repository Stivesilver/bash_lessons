<?php

    Security::init();

    $dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
    $RefID = io::geti('RefID');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

	$set_ini = IDEAFormat::getIniOptions();
	$iepYearTitle = array_key_exists('iep_year_title', $set_ini) ? $set_ini['iep_year_title'] : 'IEP Year';
	$iepTitle = array_key_exists('iep_title', $set_ini) ? $set_ini['iep_title'] : 'IEP';

    $edit = new EditClass('edit1', $RefID);

    $edit->title = 'New ' . $iepYearTitle . ' Process';

    $edit->setSourceTable('webset.std_iep_year', 'siymrefid');

    $edit->addGroup('General Information');

    $edit->addControl('Anticipated ' . $iepTitle . ' Initiation Date', 'date')
        ->sqlField('siymiepbegdate')
        ->name('siymiepbegdate')
        ->req();

    /** @var FFDateTime */
    $siymiependdate = $edit->addControl('Anticipated ' . $iepTitle . ' Annual Review Date', 'date')
        ->sqlField('siymiependdate')
        ->name('siymiependdate')
        ->req();

	if (IDEACore::disParam(47) != 'N') {
		$siymiependdate->sql("SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '1 YEAR' - INTERVAL '1 DAY'")
			->tie('siymiepbegdate')
			->opts(FormFieldOptions::PROCESS_TIE_BY_CHANGE_ONLY);
	}

	$edit->addUpdateInformation();

    $edit->addControl('Student ID', 'hidden')
	    ->value($tsRefID)
	    ->sqlField('stdrefid');
	//se($ds->safeGet('refresh_screen_js'));
	$edit->onSaveDone = $ds->safeGet('refresh_screen_js');

    if ($RefID == 0) $edit->setPostsaveCallback('processYears', 'iep_proc.inc.php');

    $edit->saveAndAdd = false;
    $edit->firstCellWidth = "40%";

    $edit->addSQLConstraint(
	    $iepTitle . ' Annual Review Date should be greater than ' . $iepTitle . ' Initiation Date',
	    "SELECT 1 WHERE '[siymiepbegdate]' >= '[siymiependdate]' AND '[siymiependdate]'!= '0' AND '[siymiependdate]'!= '0'"
    );

    $edit->printEdit();
?>
