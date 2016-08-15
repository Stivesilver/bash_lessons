<?php

	Security::init();

	$role = io::post('role');

	if (IDEACore::disParam(35) == 'Y') {
		$SQL = "
			SELECT seq_num
              FROM webset.disdef_participantrolesdef
             WHERE prddesc = '$role' and vndrefid = VNDREFID
            ";
	} else {
		$SQL = "
			SELECT seq_num
              FROM webset.statedef_participantrolesdef
             WHERE prddesc = '$role'
               AND screfid = " . VNDState::factory()->id . "
	        ";
	}

	$result = db::execSQL($SQL)->assoc();

	io::ajax('seqNum', (int)$result['seq_num']);

?>