<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::posti('RefID');
    $constr = io::get('constr');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    foreach ($_POST as $key => $val) {
        if ($val != '' and substr($key, 0, 7) == 'constr_') $values[substr($key, 7, strlen($key))] = stripslashes($val);
    }

    if ($RefID > 0) {
        DBImportRecord::factory('webset.std_forms', 'smfcrefid')
            ->key('smfcrefid', $RefID)
            ->set('smfcdate', 'NOW()', true)
            ->set('fdf_content', base64_encode(IDEAFormPDF::fdf_prepare($values, io::post('smfcfilename'), io::posti('mfcrefid'))))
            ->set('lastuser', SystemCore::$userUID)
            ->set('lastupdate', 'NOW()', true)
            ->import();
    } else {
        $RefID = DBImportRecord::factory('webset.std_forms', 'smfcrefid')
            ->set('stdrefid', $tsRefID)
            ->set('smfcdate', 'NOW()', true)
            ->set('iepyear', $stdIEPYear, true)
            ->set('mfcrefid', io::posti('mfcrefid'))
            ->set('smfcfilename', io::post('smfcfilename'))
            ->set('fdf_content', base64_encode(IDEAFormPDF::fdf_prepare($values, io::post('smfcfilename'), io::posti('mfcrefid'))))
            ->set('lastuser', SystemCore::$userUID)
            ->set('lastupdate', 'NOW()', true)
            ->import()
            ->recordID();
    }

    if (io::post('finishFlag') == 'no') {
        header('Location: ' . CoreUtils::getURL('frm_xml.php', array_merge($_GET, array('RefID' => $RefID))));
    } else {
        header('Location: ' . CoreUtils::getURL('frm_main.php', $_GET));
    }
?>