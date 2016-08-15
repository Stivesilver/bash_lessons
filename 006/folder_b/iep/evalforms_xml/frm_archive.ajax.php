<?php

    Security::init();

    $RefIDs = explode(',', io::post('RefID'));
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            DBImportRecord::factory('webset.std_forms_xml', 'sfrefid')
                ->key('sfrefid', $RefIDs[$i])
                ->set('archived', 'Y')
                ->set('lastuser', db::escape(SystemCore::$userUID))
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }
    }
?>
