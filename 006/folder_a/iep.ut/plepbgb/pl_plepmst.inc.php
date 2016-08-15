<?php

	function preSave($RefID, &$data) {
		IDEABackup::factory($data['stdrefid'], 59)->backupData('webset.std_plepmst', 'stdrefid', $data['stdrefid']);
	}

?>
