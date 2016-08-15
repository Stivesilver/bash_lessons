<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
    $student = IDEAStudent::factory($tsRefID);

    $RefIDs = explode(',', io::post('RefID'));
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            if ($RefIDs[$i] == $student->get('stdiepyear')) {
                io::msg('Current IEP Year can not be deleted.', false);
            } else {
                DBImportRecord::factory('webset.std_placementcode', 'pcrefid')
                    ->key('pcrefid', $RefIDs[$i])
                    ->set('stdrefid', null)
                    ->set('lastuser', SystemCore::$userUID . '_' . $tsRefID)
                    ->set('lastupdate', 'NOW()', true)
                    ->import();
            }
        }
    }
?>