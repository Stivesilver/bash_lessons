<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
    $student = IDEAStudent::factory($tsRefID);

	$set_ini = IDEAFormat::getIniOptions();
	$iepYearTitle = array_key_exists('iep_year_title', $set_ini) ? $set_ini['iep_year_title'] : 'IEP Year';

    $RefIDs = array_map('intval', explode(',', io::post('RefID')));
	$error = null;
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            if ($RefIDs[$i] == $student->get('stdiepyear')) {
	            $error = 'Current ' . $iepYearTitle . ' can not be deleted.';
            } else {
                DBImportRecord::factory('webset.std_iep_year', 'siymrefid')
                    ->key('siymrefid', $RefIDs[$i])
                    ->set('stdrefid', null)
                    ->set('dsyrefid', 'stdrefid', true)
                    ->setUpdateInformation()
                    ->import();
            }
        }
    }
	if ($error) io::msg($error, false);
?>