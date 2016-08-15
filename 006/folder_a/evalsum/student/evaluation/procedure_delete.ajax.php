<?php

    Security::init();

    $RefIDs = explode(',', io::post('RefID'));
    $haveArchived = false;

    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        #Avoid Archived form deletion
        if (db::execSQL("
                SELECT archived
                  FROM webset.es_std_scr
                 WHERE shsdrefid = " . $RefIDs[$i] . "
            ")->getOne() == 'Y') {
            $haveArchived = true;
            continue;
        }

        DBImportRecord::factory('webset.es_std_scr', 'shsdrefid')
            ->key('shsdrefid', $RefIDs[$i])
            ->set('eprefid', 'NULL', true)
            ->set('deleted_id', 'eprefid', true)
            ->setUpdateInformation()
            ->import();
    }

    if ($haveArchived) io::msg('You can not delete Archived Documents', false);
?>
