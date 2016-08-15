<?php

    Security::init();

    $RefIDs = explode(',', io::post('RefID'));
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            DBImportRecord::factory('webset.es_std_scr', 'shsdrefid')
                ->key('shsdrefid', $RefIDs[$i])
                ->set('archived', "CASE archived WHEN 'Y' THEN NULL ELSE 'Y' END ", true)
                ->set('lastuser', SystemCore::$userUID)
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }
    }
?>
