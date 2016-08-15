<?php

	function saveProgRep($RefID, &$data) {
		$sprRefID      = io::posti('spr_refid');
		$smpRefid      = io::posti('smp_refid');
		$sprPeriodData = json_decode(io::post('spr_period_data'), true);
		$idGoal        = '';
		$idBench       = '';

		/** @var array $sprPeriodData change data about period or create new key with data */
		/** @var int $smpRefid */
		$sprPeriodData[$smpRefid] = array(
			'narrative'      => io::post('narrative'),
			'extentProgress' => io::posti('extentProgress')
		);

		if (io::post('type') == 'g') {
			$idGoal = io::post('id');
		} else {
			$idBench = io::post('id');
		}

		DBImportRecord::factory('webset.std_progress_reporting', 'spr_refid')
			->key('spr_refid'      , (int)$sprRefID)
			->set('sbg_grefid'     , $idGoal)
			->set('sbb_brefid'     , $idBench)
			->set('spr_period_data', json_encode($sprPeriodData))
			->set('lastuser', SystemCore::$userUID)
			->set('lastupdate', date('m-d-Y H:i:s'))
			->import();

	}

?>
