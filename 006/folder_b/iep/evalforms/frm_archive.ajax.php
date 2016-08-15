<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
    $student = IDEAStudent::factory($tsRefID);

    $RefIDs = explode(',', io::post('RefID'));
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            DBImportRecord::factory('webset.std_forms', 'smfcrefid')
                ->key('smfcrefid', $RefIDs[$i])
                ->set('archived', 'Y')
                ->set('lastuser', SystemCore::$userUID)
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }
    }
?>