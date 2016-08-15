<?php

	Security::init();

	$RefID = io::geti('RefID');

	$benchmark_id = io::geti('benchmark_id');
	$goal_id = io::geti('goal_id');
	$dskey = io::get('dskey');
	$as_refid = io::geti('as_refid');

	$ds = DataStorage::factory($dskey);
	$student = $ds->safeGet('stdname') . " (" . $ds->safeGet('grdlevel') . ")";

	$trial = db::execSQL("
		SELECT trial_num,
			   trial_date
		  FROM webset.std_bgb_assessment_entry AS en
		 WHERE en.en_refid = $RefID
		   AND vndrefid = VNDREFID
	")
	->assoc();

	$data_collection_method = db::execSQL("
		SELECT data_collection_method
		  FROM webset.std_bgb_assessment
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->getOne();

	$trial_array = db::execSQL("
		SELECT trial_num
		  FROM webset.std_bgb_assessment_entry AS en
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->indexCol();

	$trial_num = db::execSQL("
		SELECT num_trial
		  FROM webset.std_bgb_assessment
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->getOne();

	$trial_num = current(array_diff(range(1, $trial_num), $trial_array));

	if ($trial_num == '') {
		$trial_num = db::execSQL("
			SELECT MAX(trial_num)
			  FROM webset.std_bgb_assessment_entry AS en
			 WHERE as_refid = $as_refid
			   AND vndrefid = VNDREFID
		")
		->getOne() + 1;
	}

	$ind_refids = db::execSQL("
		SELECT ind.ind_refid
		  FROM webset.std_bgb_indicator AS ind
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->indexCol();

	$edit = new EditClass('Edit', $RefID);

	$edit->title = 'Add Benchmark Measurement';

	$edit->setSourceTable('webset.std_bgb_assessment_entry', 'en_refid');

	$edit->addControl('Student', 'protected')
		->value($student);

	$edit->addControl('Date of Measurements', 'date')
		->value(date('Y-m-d H:i:s'))
		->sqlField('date_of_measure')
		->name('date_of_measure');

	$edit->addControl(FFUserSearch::factory())
		->caption('Recorded by')
		->sqlField('recorded_by')
		->name('recorded_by')
		->value(SystemCore::$userID);

	$edit->addControl(FFUserSearch::factory())
		->caption('Entered by')
		->sqlField('entered_by')
		->name('entered_by')
		->value(SystemCore::$userID);

	$edit->addGroup('Trial #' . ($trial['trial_num'] ? $trial['trial_num'] : $trial_num));
	$edit->addControl('trial', 'hidden')
		->sqlField('trial_num')
		->name('trial_num')
		->value($trial['trial_num'] ? $trial['trial_num'] : $trial_num);

	$edit->addControl('Date/Time', 'datetime')
		->sqlField('trial_date')
		->name('trial_date')
		->value($trial['trial_date'] ? $trial['trial_date'] : date('Y-m-d H:i:s'));

	$edit->addControl(FFSwitchYN::factory('Used for Baseline'))
		->sqlField('used_for_baseline')
		->name('used_for_baseline')
		->value('Y');

	$measure_indicator = '';
	if ($RefID) {
		$measure_indicator = db::execSQL("
			SELECT m.m_refid,
				   m.desc_measure,
			       ind.ind_refid,
			       ind.ind_symbol,
			       mi.mi_refid
			  FROM webset.std_bgb_measurement_benchmark AS mb
				   INNER JOIN webset.std_bgb_measurement_indicator AS mi ON mi.mi_refid = mb.mi_refid
			       INNER JOIN webset.std_bgb_measurement AS m ON m.m_refid = mi.m_refid
			       INNER JOIN webset.std_bgb_indicator AS ind ON ind.ind_refid = mi.ind_refid
			 WHERE mb.en_refid = $RefID
		")
		->assocAll();
	} else {
		$measure_indicator = db::execSQL("
			SELECT m.m_refid,
				   m.desc_measure,
				   NULL AS ind_refid
			  FROM webset.std_bgb_measurement AS m
			 WHERE m.as_refid = $as_refid
		")
		->assocAll();
	}

	$sel_indicator = FFButton::factory('Apply Selected Indicator', 'setIndicator()')
		->help('If you wish to automatically assign an Indicator value to all Measurement Items, click on the Indicator to select it and then click on the Apply Selected Indicator option.')
		->toHTML();

	$indicators = db::execSQL("
		SELECT ind.ind_refid,
			   ind_symbol
		  FROM webset.std_bgb_indicator AS ind
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	");

	$edit->addControl('Measurement Indicator', 'select_radio')
		->data($indicators->keyedCol())
		->value($indicators->getOne())
		->name('default_indicator')
		->append($sel_indicator);

	$data = array();

	$j = 0;
	foreach ($measure_indicator as $key => $value) {
		$sql_indicator = "
			SELECT ind.ind_refid,
				   ind_symbol
			  FROM webset.std_bgb_indicator AS ind
				   INNER JOIN webset.std_bgb_measurement_indicator AS mi ON mi.ind_refid = ind.ind_refid
			 WHERE as_refid = $as_refid
			   AND m_refid = " . $value['m_refid'] . "
			   AND vndrefid = VNDREFID
		";
		switch ($data_collection_method) {
			case 'T':
				$pad_lock = FFPadlock::factory()
					->value('N');
				$layout = getAddingIndicators($sql_indicator, $pad_lock, $j++, $value['m_refid'], $value['ind_refid']);
				$edit->addControl($value['desc_measure'], 'select')
					->value($value['ind_refid'])
					->name('measure_' . $value['m_refid'])
					->sql($sql_indicator)
					->disabledIf($pad_lock, 'Y')
					->append($layout);
				break;
			case 'C':
				$edit->addControl($value['desc_measure'], 'select')
					->value($value['ind_refid'])
					->name('measure_' . $value['m_refid'])
					->sql($sql_indicator);
				break;
		}
	}

	function getAddingIndicators($sql_indicator, $pad_lock, $j, $m_refid, $ind_refid) {
		global $RefID;
		$ui_layout = UILayout::factory();
		$ui_layout->addObject($pad_lock);

		$ind = db::execSQL($sql_indicator)->assocAll();
		$sum = db::execSQL("
			SELECT SUM(result)
			  FROM webset.std_bgb_trials AS sbt
				   INNER JOIN webset.std_bgb_measurement_benchmark AS sbmb ON sbmb.mb_refid = sbt.mb_refid
				   INNER JOIN webset.std_bgb_measurement_indicator AS sbmi ON sbmi.mi_refid = sbmb.mi_refid
			 WHERE sbmi.ind_refid = $ind_refid
			   AND sbmi.m_refid = $m_refid
			   AND sbmb.en_refid = $RefID
		")
		->getOne();
		foreach ($ind as $key => $value) {
			$results = db::execSQL("
				SELECT result
				  FROM webset.std_bgb_trials AS sbt
					   INNER JOIN webset.std_bgb_measurement_benchmark AS sbmb ON sbmb.mb_refid = sbt.mb_refid
				       INNER JOIN webset.std_bgb_measurement_indicator AS sbmi ON sbmi.mi_refid = sbmb.mi_refid
				 WHERE sbmi.ind_refid = $ind_refid
				   AND sbmi.m_refid = $m_refid
				   AND sbmb.en_refid = $RefID
				   AND sbt.ind_refid = " . $value['ind_refid']
			)
			->getOne();
			$ui_layout->addObject(
				UILayout::factory()
					->addHTML('', '15px')
					->addHTML($value['ind_symbol'])
					->addObject(
						FFInput::factory(FFInput::INT_NUMBER)
							->name($j . '_ind_' . $value['ind_refid'])
							->width('50px')
							->value($results)
							->onChange('change_percent(' . $j . ', ' . $m_refid . ')')
							->onBlur('change_percent(' . $j . ', ' . $m_refid . ')')
					)
					->addObject(
						FFEmpty::factory()
							->name($j .'_percent_' . $value['ind_refid'])
							->value(round($sum != 0 ? ($results / $sum * 100) : 0, 0))
							->append('%')
					)
			);
		}

		return $ui_layout->toHTML();
	}

	$edit->addGroup('Update Information')
		->collapsed(true);

	$edit->addControl('Last User', 'protected')
		->sqlField('lastuser')
		->value(SystemCore::$userUID);

	$edit->addControl('Last Update', 'protected')
		->sqlField('lastupdate')
		->value(date('m-d-Y H:i:s'));

	$edit->addControl('vndrefid', 'hidden')
		->sqlField('vndrefid')
		->value(SystemCore::$VndRefID);

	$edit->addControl('as_refid', 'hidden')
		->sqlField('as_refid')
		->name('as_refid')
		->value($as_refid);

	$edit->setPostsaveCallback('saveBenchmark', './bgb_assessment_entry_save.inc.php');

	$edit->finishURL = CoreUtils::getURL(
		'./bgb_assessment_entry_list.php',
		array(
			'goal_id' => $goal_id,
			'benchmark_id' => $benchmark_id,
			'dskey' => $dskey
		)
	);

	$edit->cancelURL = CoreUtils::getURL(
		'./bgb_assessment_entry_list.php',
		array(
			'goal_id' => $goal_id,
			'benchmark_id' => $benchmark_id,
			'dskey' => $dskey
		)
	);

	$edit->saveAndAdd = false;

	$edit->printEdit();

	io::jsVar('measure_indicator', $measure_indicator);
	io::jsVar('ind_refids', $ind_refids);
?>
<script type="text/javascript">
	function setIndicator() {
		var default_indicator = $('#default_indicator').val();
		var i = 0;

		for (i = 0; i < measure_indicator.length; i++) {
			$('#measure_' + measure_indicator[i]['m_refid']).val(default_indicator);
		}
	}

	function change_percent(indicator_number, m_refid) {
		var ind_value = {};
		for (i = 0; i < ind_refids.length; i++) {
			ind_value[ind_refids[i]] = $('#' + indicator_number + '_ind_' + ind_refids[i]).val();
		}

		var sum = 0;
		for (var key in ind_value) {
			sum = sum + parseInt(!ind_value[key] ? 0 : ind_value[key]);
		}

		var max_ind_value = '';
		if (sum != 0) {
			var max = 0;
			for (var key in ind_value) {
				var res = Math.round(parseInt(ind_value[key] == '' ? 0 : ind_value[key]) / sum * 100);
				if (max < res) {
					max_ind_value = key;
					max = res;
				}
				ind_value[key] = res;
			}
		}

		for (var i in ind_value) {
			$('#' + indicator_number + '_percent_' + i).val(ind_value[i]).change();
		}
		if ($('#' + 'measure_' + m_refid).attr('disabled') != 'disabled') {
			$('#' + 'measure_' + m_refid).val(max_ind_value).change();
		}
	}
</script>