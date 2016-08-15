<?php

	function saveOutcome($RefID, /** @noinspection PhpUnusedParameterInspection */$data, $params) {

		$stn_refid = $params['stn_refid'];
		if ($stn_refid) {
			$SQL = "
				DELETE
				  FROM webset.std_tn_ns_goal
				 WHERE stn_refid = " . $stn_refid . "
			";
			db::execSQL($SQL);
		}
		$grefids = io::get('grefids');
		if ($grefids) {
			$grefids = array_map('intval', explode(',', io::get('grefids')));

			foreach ($grefids as $grefid) {
				DBImportRecord::factory('webset.std_tn_ns_goal', 'stng_refid')
					->set('grefid', $grefid)
					->set('stn_refid', $RefID)
					->setUpdateInformation()
					->import(DBImportRecord::INSERT_ONLY);
			}
		}
	}
?>