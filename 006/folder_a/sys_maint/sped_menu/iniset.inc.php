<?php

	function backupPrevious($RefID, &$data) {
		IDEABackup::factory(null, 'webset.sped_ini_set')->backupData('isrefid', $RefID);
	}

?>
