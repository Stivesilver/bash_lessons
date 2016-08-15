<?php

	function updateIEP($RefID, &$data) {
		//updating all rest records
		$SQL = "
	        UPDATE webset.std_iep_year
	           SET siymcurrentiepyearsw = NULL
	         WHERE siymrefid != " . $RefID . "
	           AND stdrefid = " . $data['stdrefid'] . "
        ";
		db::execSQL($SQL);

		//updating selected record
		DBImportRecord::factory('webset.std_iep_year', 'siymrefid')
			->key('siymrefid', $RefID)
			->set('siymcurrentiepyearsw', 'Y')
			->set('lastuser', SystemCore::$userUID)
			->set('lastupdate', 'NOW()', true)
			->import();

	}

?>