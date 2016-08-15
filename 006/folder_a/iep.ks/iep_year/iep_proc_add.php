<?php

    Security::init();

    $dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
    $RefID = io::geti('RefID');
    $tsRefID = $ds->safeGet('tsRefID');

    /*     * HERE WE CHECK IF DISTRICT MAY COPY ENTRIES
      if (disParam(4) == "Y") {
      $mes = "The system will now create a New IEP Year copying the previous year\'s Baseline/Goals and Benchmarks into the New IEP Year. Please continue to review all IEP data for your student to insure the appropriate plan is implemented. Do You Wish to Continue?";
      }  else {
      $mes = "The system will now create a New IEP Year. Baseline/Goals and Benchmarks have been deleted. You must enter new Baseline/Goals and Benchmarks for the New IEP Year. Please continue to review all IEP data for your student to insure the appropriate plan is implemented. Do You Wish to Continue?";
      }
     */

    $edit = new EditClass('edit1', $RefID);

    $edit->title = 'New IEP Year Process';

    $edit->setSourceTable('webset.std_iep_year', 'siymrefid');

    $edit->addGroup('General Information');

    $edit->addControl('Anticipated IEP Initiation Date', 'date')
        ->sqlField('siymiepbegdate')
        ->name('siymiepbegdate')
        ->req();

    /** @var FFDateTime */
    $siymiependdate = $edit->addControl('Anticipated IEP Annual Review Date', 'date')
        ->sqlField('siymiependdate')
        ->name('siymiependdate')
        ->req();

    if (IDEACore::disParam(47) != "N") {
        $siymiependdate->sql("
            SELECT (NULLIF('VALUE_01','')::DATE + INTERVAL '1 YEAR' - INTERVAL '1 DAY')::DATE
        ")->tie('siymiepbegdate');
    }

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

    $edit->finishURL = CoreUtils::getURL('iep_proc.php', array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL('iep_proc.php', array('dskey' => $dskey));
	$edit->onSaveDone = $ds->safeGet('refresh_screen_js');

    if ($RefID == 0) $edit->setPostsaveCallback('processYears', 'iep_proc.inc.php');

    $edit->saveAndAdd = false;
    $edit->firstCellWidth = "40%";

    $edit->addSQLConstraint('IEP Annual Review Date should be greater than IEP Initiation Date', "
        SELECT 1 WHERE '[siymiepbegdate]' >= '[siymiependdate]' AND '[siymiependdate]'!= '0' AND '[siymiependdate]'!= '0'
    ");

    $edit->printEdit();
?>
