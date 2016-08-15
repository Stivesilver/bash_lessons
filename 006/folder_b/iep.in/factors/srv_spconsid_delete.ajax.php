<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
    $student = IDEAStudent::factory($tsRefID);

    $RefIDs = explode(',', io::post('RefID'));
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            $SQL = "
                SELECT formrefid
                  FROM webset.std_spconsid
                 WHERE sscmrefid = " . $RefIDs[$i] . "
            ";
            $spconsid = db::execSQL($SQL)->assoc();

            //Delete XML FORM ENTRY
            if ($spconsid["formrefid"] > 0) {
                DBImportRecord::factory('webset.std_forms_xml', 'sfrefid')
                    ->key('sfrefid', $spconsid["formrefid"])
                    ->set('stdrefid', 'NULL', true)
                    ->set('lastuser', db::escape(SystemCore::$userUID))
                    ->set('lastupdate', 'NOW()', true)
                    ->import();
            }
            //Delete SP Consideration Entry
            db::execSQL("DELETE FROM webset.std_spconsid WHERE sscmrefid = " . $RefIDs[$i]);
        }
    }
?>