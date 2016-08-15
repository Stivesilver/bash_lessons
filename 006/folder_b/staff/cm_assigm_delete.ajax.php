<?php
    Security::init();

    $RefIDs = explode(',', io::post('RefID'));
    for ($i=0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
             DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
                 ->key('tsrefid', $RefIDs[$i])
                 ->set('umrefid', null)
                 ->set('lastuser', db::escape(SystemCore::$userUID))
                 ->set('lastupdate', 'NOW()', true)
                 ->import();
        }
    }
?>
