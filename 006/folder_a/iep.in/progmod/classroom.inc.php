<?php

	function savePurpose($RefID, &$data) {

		db::execSQL("
        	DELETE FROM webset.std_srv_progmod
        	 WHERE stdrefid = " . io::post('RefID', true) . "
        ");

		$RefIDs = explode(',', io::post('progmods'));

		for ($i = 0; $i < sizeOf($RefIDs); $i++) {
			if ($RefIDs[$i] > 0) {
				$dbrec = DBImportRecord::factory('webset.std_srv_progmod', 'ssmrefid')
					->set('stdrefid', io::post('RefID', true))
					->set('stsrefid', $RefIDs[$i])
					->set('lastuser', db::escape(SystemCore::$userUID))
					->set('lastupdate', 'NOW()', true);
				$dbrec->import();
			}
		}

		if (io::post('other') != '') {
			DBImportRecord::factory('webset.std_srv_progmod', 'ssmrefid')
				->set('stdrefid', io::post('RefID', true))
				->set('ssmshortdesc', io::post('other'))
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import();
		}
	}

?>
