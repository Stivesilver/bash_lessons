<?php

	function setCurrentYear($RefID, /** @noinspection PhpUnusedParameterInspection */$data, $d) {

		$tsRefID = DataStorage::factory($d['dskey'])->safeGet('tsRefID');
		db::beginTrans();
		# updating all rest records
		$SQL = "
	        UPDATE webset.std_iep_year
	           SET siymcurrentiepyearsw = NULL
	         WHERE siymrefid != " . (int)$RefID . "
	           AND stdrefid = " . (int)$tsRefID . "
	    ";
		db::execSQL($SQL);

		# updating selected record
		DBImportRecord::factory('webset.std_iep_year', 'siymrefid')
			->key('siymrefid', $RefID)
			->set('siymcurrentiepyearsw', 'Y')
			->setUpdateInformation()
			->import();

		# Update IEP Initiation Date and IEP Projected Date of Annual Review of IEP DATES
		if (VNDState::factory()->id == "16") {
			$SQL = "
	            UPDATE webset.sys_teacherstudentassignment
	               SET stdEnrollDT = siymiepbegdate,
	                   stdCmpltDT  = siymiependdate,
	                   lastuser = '" . SystemCore::$userUID . "',
	                   lastupdate = now()
	              FROM webset.std_iep_year
	             WHERE siymrefid = " . (int)$RefID . "
	               AND tsrefid = " . (int)$tsRefID . "
	        ";
			db::execSQL($SQL);
		}
		db::commitTrans();
	}
?>