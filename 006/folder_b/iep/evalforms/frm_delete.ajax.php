<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
    $student = IDEAStudent::factory($tsRefID);

    $RefIDs = explode(',', io::post('RefID'));
    $haveArchived = false;
    $haveConsider = false;
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        #Avoid Archived form deletion
        if (db::execSQL("
                SELECT archived
                  FROM webset.std_forms
                 WHERE smfcrefid = " . $RefIDs[$i] . "
                        ")->getOne() == 'Y') {
            $haveArchived = true;
            continue;
        }
        #Avoid Sp Cponsiderations form deletion
        if (db::execSQL("
                SELECT 1
                  FROM webset.std_spconsid
                 WHERE COALESCE(pdf_refid,formrefid) = " . $RefIDs[$i] . "
            ")->getOne() == '1') {
            $haveConsider = true;
            continue;
        }

        DBImportRecord::factory('webset.std_forms', 'smfcrefid')
            ->key('smfcrefid', $RefIDs[$i])
            ->set('stdrefid', 'NULL', true)
            ->set('lastuser', SystemCore::$userUID)
            ->set('lastupdate', 'NOW()', true)
            ->import();
    }

    if ($haveArchived) io::msg('You can not delete Archived Documents', false);
    if ($haveConsider) io::msg('You can not delete Documents created in Special Considerations', false);
?>