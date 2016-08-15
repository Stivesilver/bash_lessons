<?php

    Security::init();
    //require_once("$g_physicalRoot/applications/webset/iep/error/err_check.inc.php");
    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

    //updating all rest records
    $SQL = "
        UPDATE webset.std_iep_year
           SET siymcurrentiepyearsw = NULL
         WHERE siymrefid != " . $RefID . "
           AND stdrefid = " . $tsRefID . "
    ";
    db::execSQL($SQL);

    //updating selected record
    DBImportRecord::factory('webset.std_iep_year', 'siymrefid')
        ->key('siymrefid', $RefID)
        ->set('siymcurrentiepyearsw', 'Y')
        ->set('lastuser', SystemCore::$userUID)
        ->set('lastupdate', 'NOW()', true)
        ->import();

    //Update IEP Initiation Date and IEP Projected Date of Annual Review of IEP DATES
    if (VNDState::factory()->id == "16") {
        $SQL = "
            UPDATE webset.sys_teacherstudentassignment
               SET stdEnrollDT = siymiepbegdate,
                   stdCmpltDT  = siymiependdate,
                   lastuser = '" . SystemCore::$userUID . "',
                   lastupdate = now()
              FROM webset.std_iep_year
             WHERE siymrefid = " . $RefID . "
               AND tsrefid = " . $tsRefID . "
        ";
        db::execSQL($SQL);
    }
    //checkStdErrors("null");
    header('Location: ' . CoreUtils::getURL('iep_cur.php', array('dskey' => $dskey, 'refresh' => 1)));
?>
