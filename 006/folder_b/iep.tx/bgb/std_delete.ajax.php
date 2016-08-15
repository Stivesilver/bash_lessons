<?php

	Security::init();

	$RefIDs = explode(',', io::post('RefID'));

	for ($i = 0; $i < sizeOf($RefIDs); $i++) {

		if ($RefIDs[$i] > 0) {
			if (io::get('mode') == 'goal') {
				$SQL = "
                    UPDATE webset_tx.std_sb_objectives
                       SET deleted_id = grefid,
                           grefid = NULL,
                           lastuser = '" . SystemCore::$userUID . "',
                           lastupdate = NOW()
                     WHERE grefid = " . $RefIDs[$i] . ";

                    UPDATE webset_tx.std_sb_goals
                       SET deleted_id = iepyear,
                           iepyear = NULL,
                           lastuser = '" . SystemCore::$userUID . "',
                           lastupdate = NOW()
                     WHERE grefid = " . $RefIDs[$i] . ";
                ";
			} elseif (io::get('mode') == 'objective') {
				$SQL = "
                    UPDATE webset_tx.std_sb_objectives
                       SET deleted_id = grefid,
                           grefid = NULL,
                           lastuser = '" . SystemCore::$userUID . "',
                           lastupdate = NOW()
                     WHERE orefid = " . $RefIDs[$i] . ";
                ";
			}
			db::execSQL($SQL);
		}
	}
?>