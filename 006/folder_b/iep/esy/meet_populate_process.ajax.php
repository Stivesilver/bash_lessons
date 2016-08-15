<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');
    $stdIEPYear = DataStorage::factory($dskey)->safeGet('stdIEPYear');
    $iepmode = io::get('iepmode');

    $RefIDs = explode(',', io::post('RefID'));
    for ($i = 0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {
            $user = db::execSQL("
			    SELECT *
			      FROM webset.std_iepparticipants
			     WHERE spirefid = " . $RefIDs[$i] . "
			")->assoc();
            DBImportRecord::factory('webset.std_esy_participants', 'spirefid')
                ->set('stdrefid', $tsRefID)
                ->set('participantname', $user['participantname'])
                ->set('participantrole', $user['participantrole'])
                ->set('participantatttype', $user['participantatttype'])
                ->set('std_seq_num', $user['std_seq_num'])
                ->set('iep_year', $iepmode == '1' ? $stdIEPYear : null)
                ->set('lastuser', SystemCore::$userUID)
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }
    }
?>