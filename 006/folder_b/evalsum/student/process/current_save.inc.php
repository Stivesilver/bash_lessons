<?php
	function postSave($data, $info, $key) {
		$tsRefID = DataStorage::factory($key['dskey'])->safeGet('tsRefID');

		//updating all rest records
		$SQL = "
	        UPDATE webset.es_std_evalproc
	           SET ep_current_sw = NULL
	         WHERE eprefid != " . $data . "
	           AND stdrefid = " . $tsRefID . "
	    ";
		db::execSQL($SQL);

		//updating selected record
		DBImportRecord::factory('webset.es_std_evalproc', 'eprefid')
			->key('eprefid', $data)
			->set('ep_current_sw', 'Y')
			->set('lastuser', SystemCore::$userUID)
			->set('lastupdate', 'NOW()', true)
			->import();
	}

?>
