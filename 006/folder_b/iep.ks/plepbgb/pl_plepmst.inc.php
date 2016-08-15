<?php

	function preSave($RefID, &$data) {
		IDEABackup::factory($data['stdrefid'], 'webset.std_plepmst', 60)->backupData('stdrefid', $data['stdrefid']);
	}

?>
