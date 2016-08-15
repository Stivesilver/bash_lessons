<?php

    Security::init();

    $RefIDs = explode(',', io::post('RefID'));
    $haveArchived = false;

    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        #Avoid Archived form deletion
        if (db::execSQL("
                SELECT archived 
                  FROM webset.es_std_evalproc_forms
                 WHERE frefid = " . $RefIDs[$i] . "
            ")->getOne() == 'Y') {
            $haveArchived = true;
            continue;
        }

        DBImportRecord::factory('webset.es_std_evalproc_forms', 'frefid')
            ->key('frefid', $RefIDs[$i])
            ->set('evalproc_id', 'NULL', true)
            ->set('deleted_id', 'evalproc_id', true)
            ->set('lastuser', SystemCore::$userUID)
            ->set('lastupdate', 'NOW()', true)
            ->import();
    }

    if ($haveArchived) io::msg('You can not delete Archived Documents', false);
?>
