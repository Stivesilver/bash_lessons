<?php

	function saveProvided($RefID, /** @noinspection PhpUnusedParameterInspection */$data, $values) {
		db::beginTrans();
		$SQL = "
			DELETE
			  FROM webset.std_tn_serv_summ_rc
			 WHERE grefid = " . $RefID . ";

			DELETE
			  FROM webset.std_tn_serv_summ_nr
			 WHERE grefid = " . $RefID . ";
		";
		db::execSQL($SQL);

		foreach ($values['stn_refids'] as $stn_refid) {
			$stsr_provided = io::get('stsr_provided_' . $stn_refid);
			if (!$stsr_provided) continue;

			DBImportRecord::factory('webset.std_tn_serv_summ_rc', 'stsr_refid')
				->set('stn_refid', $stn_refid)
				->set('grefid', $RefID)
				->set('stsr_provided', $stsr_provided)
				->setUpdateInformation()
				->import(DBImportRecord::INSERT_ONLY);
		}

		foreach ($values['sns_refids'] as $sns_refid) {
			$stsn_provided = io::get('stsn_provided_' . $sns_refid);
			if (!$stsn_provided) continue;

			DBImportRecord::factory('webset.std_tn_serv_summ_nr', 'stsn_refid')
				->set('sns_refid', $sns_refid)
				->set('grefid', $RefID)
				->set('stsn_provided', $stsn_provided)
				->setUpdateInformation()
				->import(DBImportRecord::INSERT_ONLY);
		}

		db::commitTrans();
	}
?>