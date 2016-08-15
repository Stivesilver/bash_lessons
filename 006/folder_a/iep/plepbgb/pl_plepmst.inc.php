<?php

	function preSave($RefID, &$data) {
		IDEABackup::factory($data['stdrefid'], 'webset.std_plepmst', 59)->backupData('iepyear', $data['iepyear']);
	}

?>
