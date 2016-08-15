<?php

	Security::init();

	$as_refid = io::geti('as_refid');
	$num_trial = io::geti('num_trial');

	$ind_refids = db::execSQL("
		SELECT ind.ind_refid
		  FROM webset.std_bgb_indicator AS ind
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->indexCol();

	db::beginTrans();
	for ($i = 0; $i < $num_trial; $i++) {
		$newRefID = DBImportRecord::factory('webset.std_bgb_assessment_entry', 'en_refid')
			->set('as_refid', $as_refid)
			->set('trial_num', io::get('trial_num_' . $i))
			->set('trial_date', io::get('trial_date_' . $i))
			->set('date_of_measure', io::get('date_of_measure'))
			->set('recorded_by', io::get('recorded_by'))
			->set('entered_by', io::get('entered_by'))
			->set('used_for_baseline', io::get('used_for_baseline_' . $i))
			->set('lastuser', SystemCore::$userUID)
			->set('lastupdate', date('m-d-Y H:i:s'))
			->set('vndrefid', SystemCore::$VndRefID)
			->import()
			->recordID();

		$m_refids = db::execSQL("
			SELECT m_refid
			  FROM webset.std_bgb_measurement
			 WHERE as_refid = $as_refid"
		)
		->assocAll();

		$measuer_ind = array();
		foreach ($m_refids as $key => $value) {
			if (io::get($i . '_measure_' . $value['m_refid'])) {
				$measuer_ind[$value['m_refid']] = io::get($i . '_measure_' . $value['m_refid']);
			}
		}

		db::execSQL("
			DELETE FROM webset.std_bgb_measurement_benchmark
			 WHERE en_refid = $newRefID
		");

		$j = 0;
		foreach ($measuer_ind as $key => $value) {
			$mi_refid = db::execSQL("
				SELECT mi_refid
				  FROM webset.std_bgb_measurement_indicator
				 WHERE m_refid = $key
				   AND ind_refid = $value
			")
			->getOne();

			$mb_refid = DBImportRecord::factory('webset.std_bgb_measurement_benchmark', 'mb_refid')
				->key('en_refid', $newRefID)
				->key('mi_refid', $mi_refid)
				->set('lastupdate', date('m-d-Y H:i:s'))
				->set('lastuser', SystemCore::$userUID)
				->import()
				->recordID();

			foreach ($ind_refids as $key => $value) {
				DBImportRecord::factory('webset.std_bgb_trials', 'tr_refid')
					->key('mb_refid', $mb_refid)
					->key('ind_refid', $value)
					->set('result', io::get($i . $j . '_ind_' . $value))
					->set('lastupdate', date('m-d-Y H:i:s'))
					->set('lastuser', SystemCore::$userUID)
					->set('vndrefid', SystemCore::$VndRefID)
					->import();
			}
			$j++;
		}
	}

	$b_refid = db::execSQL("
		SELECT brefid
		  FROM webset.std_bgb_assessment
		 WHERE as_refid = " . $as_refid
	)
	->getOne();

	IDEAStudentBenchmarkAssessment::factory($b_refid)
		->analyzeBenchmarkGoal();

	db::commitTrans();

	io::js("EditClass.get().saveComplete(" . $newRefID . ")");
?>