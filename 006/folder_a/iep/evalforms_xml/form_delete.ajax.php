<?php
    Security::init();

    $RefIDs = explode(',', io::post('RefID'));
    $haveArchived = false;
    for ($i=0; $i < sizeOf($RefIDs); $i++) {
        #Avoid Archived form deletion
        if (db::execSQL("
                SELECT archived
                  FROM webset.std_forms_xml
                 WHERE sfrefid = ".$RefIDs[$i]."
                        ")->getOne()=='Y') {
            $haveArchived = true;
            continue;
        }

        DBImportRecord::factory('webset.std_fif_forms', 'sfrefid')
                ->key('sfrefid', $RefIDs[$i])
                ->set('hisrefid', 'NULL', true)
                ->set('deleted_id', 'sfrefid', true)
                ->set('lastuser', db::escape(SystemCore::$userUID))
                ->set('lastupdate', 'NOW()', true)
                ->import();
    }

    if ($haveArchived) io::msg('You can not delete Archived Documents', false);
?>
