<?php

	Security::init();

	$RefID = io::geti('RefID');

	$benchmark_id = io::geti('benchmark_id');
	$goal_id = io::geti('goal_id');
	$dskey = io::get('dskey');
	$as_refid = io::geti('as_refid');
	$num_trial = io::geti('num_trial');

	$ds = DataStorage::factory($dskey);
	$student = $ds->safeGet('stdname') . " (" . $ds->safeGet('grdlevel') . ")";

	$trial_num = db::execSQL("
		SELECT MAX(trial_num)
		  FROM webset.std_bgb_assessment_entry AS en
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->getOne();

	$data_collection_method = db::execSQL("
		SELECT data_collection_method
		  FROM webset.std_bgb_assessment
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->getOne();

	$ind_refids = db::execSQL("
		SELECT ind.ind_refid
		  FROM webset.std_bgb_indicator AS ind
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->indexCol();

	$m_refid = db::execSQL("
		 SELECT m_refid
		  FROM webset.std_bgb_measurement
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
		 ORDER BY m_refid
	")
	->indexCol();

	$trial_num = $trial_num ? ($trial_num + 1) : 1;

	$used_for_baseline = $trial_num == 1 ? 'Y' : 'N';

	$edit = new EditClass('Edit', $RefID);

	$edit->title = 'Add Benchmark Measurement';

	$edit->addControl('Student', 'protected')
		->value($student);

	$edit->addControl('Date of Measurements', 'date')
		->value(date('Y-m-d H:i:s'))
		->name('date_of_measure');

	$edit->addControl(FFUserSearch::factory())
		->caption('Recorded by')
		->name('recorded_by')
		->value(SystemCore::$userID);

	$edit->addControl(FFUserSearch::factory())
		->caption('Entered by')
		->name('entered_by')
		->value(SystemCore::$userID);

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
				   INNER JOIN webset.std_bgb_measurement_indicator AS mi ON mi.m_refid = m.m_refid
			 WHERE m.as_refid = $as_refid
			 GROUP BY m.m_refid, m.desc_measure
			 ORDER BY m.m_refid
		")
		->assocAll();
	}

	for ($i = 0; $i < $num_trial; $i++) {
		$edit->addGroup('Trial #' . ($trial_num + $i));
		$edit->addControl('trial', 'hidden')
			->name('trial_num_' . $i)
			->value($trial_num + $i);

		$edit->addControl('Date', 'datetime')
			->name('trial_date_' . $i)
			->value(date('Y-m-d H:i:s'));

		$edit->addControl(FFSwitchYN::factory('Used for Baseline'))
			->name('used_for_baseline_' . $i)
			->value($used_for_baseline);

		$sel_indicator = FFButton::factory('Apply Selected Indicator', "setIndicator('" . $i . "')")
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
			->name('default_indicator_' . $i)
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
					$layout = getAddingIndicators($sql_indicator, $pad_lock, $i, $j++, $value['m_refid']);
					$edit->addControl($value['desc_measure'], 'select')
						->value($value['ind_refid'])
						->name($i . '_measure_' . $value['m_refid'])
						->sql($sql_indicator)
						->disabledIf($pad_lock, 'Y')
						->append($layout);
					break;
				case 'C':
					$edit->addControl($value['desc_measure'], 'select')
						->value($value['ind_refid'])
						->name($i . '_measure_' . $value['m_refid'])
						->sql($sql_indicator);
					break;
			}
		}
	}

	function getAddingIndicators($sql_indicator, $pad_lock, $i, $j, $m_refid) {
		$ui_layout = UILayout::factory();
		$ui_layout->addObject($pad_lock);

		$ind = db::execSQL($sql_indicator)->assocAll();
		foreach ($ind as $key => $value) {
			$ui_layout->addObject(
				UILayout::factory()
					->addHTML('', '15px')
					->addHTML($value['ind_symbol'])
					->addObject(
						FFInput::factory(FFInput::INT_NUMBER)
							->name($i . $j . '_ind_' . $value['ind_refid'])
							->width('50px')
							->onChange('change_percent(' . $i . ', ' . $j . ', ' . $m_refid . ')')
							->onBlur('change_percent(' . $i . ', ' . $j . ', ' . $m_refid . ')')
					)
					->addObject(
						FFEmpty::factory()
							->name($i . $j .'_percent_' . $value['ind_refid'])
							->value(0)
							->append('%')
					)
			);
		}

		return $ui_layout->toHTML();
	}
	$edit->addGroup('Update Information')
		->collapsed(true);

	$edit->addControl('Last User', 'protected')
		->name('lastuser')
		->value(SystemCore::$userUID);

	$edit->addControl('Last Update', 'protected')
		->name('lastupdate')
		->value(date('m-d-Y H:i:s'));

	$edit->addControl('vndrefid', 'hidden')
		->name('vndrefid')
		->value(SystemCore::$VndRefID);

	$edit->saveURL = CoreUtils::getURL(
		'./bgb_assessment_entry_save.ajax.php',
		array(
			'as_refid' => $as_refid,
			'num_trial' => $num_trial
		)
	);

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
	io::jsVar('m_refid', $m_refid);
	io::jsVar('num_trial', $num_trial);
?>
<script type="text/javascript">
	function setIndicator(num) {
		var default_indicator = $('#default_indicator_' + num).val();
		var i = 0;

		for (i = 0; i < measure_indicator.length; i++) {
			$('#' + num + '_measure_' + measure_indicator[i]['m_refid']).val(default_indicator);
		}
	}

	function change_percent(trial_number, indicator_number, m_refid) {
		var ind_value = {};
		for (i = 0; i < ind_refids.length; i++) {
			ind_value[ind_refids[i]] = $('#' + trial_number + indicator_number + '_ind_' + ind_refids[i]).val();
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
			$('#' + trial_number + indicator_number + '_percent_' + i).val(ind_value[i]).change();
		}
		if ($('#' + trial_number + '_measure_' + m_refid).attr('disabled') != 'disabled') {
			$('#' + trial_number + '_measure_' + m_refid).val(max_ind_value).change();
		}
	}
</script>