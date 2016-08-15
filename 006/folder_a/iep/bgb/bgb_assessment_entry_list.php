<?php

	Security::init();

	$benchmark_id = io::geti('benchmark_id');

	$goal_id = db::execSQL("
		SELECT grefid
		  FROM webset.std_bgb_benchmark
		 WHERE brefid = $benchmark_id
	")
	->getOne();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$student = $ds->safeGet('stdname') . " (" . $ds->safeGet('grdlevel') . ")";

	$goal_bench = db::execSQL("
		SELECT COALESCE(goal.overridetext, goal.gsentance) AS goal_text,
			   COALESCE(bench.overridetext, bench.bsentance) AS bench_text
		  FROM webset.std_bgb_goal AS goal
			   INNER JOIN webset.std_bgb_benchmark AS bench ON bench.grefid = goal.grefid
		 WHERE goal.grefid = $goal_id
		   AND brefid = $benchmark_id
	")
	->assoc();

	$res = db::execSQL("
		SELECT as_refid, num_trial
		  FROM webset.std_bgb_assessment
		 WHERE brefid = $benchmark_id
	")
	->assoc();

	$as_refid = $res['as_refid'];
	$num_trial = $res['num_trial'];

	if ($as_refid == '') {
		die(UIMessage::factory('Setup Benchmark Assessments first', UIMessage::NOTE)->toHTML());
	}
	
	$edit = new EditClass('edit1', $as_refid);

	$edit->setSourceTable('webset.std_bgb_assessment', 'as_refid');

	$edit->title = 'Benchmark Assessment Entry';

	$edit->addGroup('Common Information')
		->collapsed(true);
	$edit->addControl('Student', 'protected')
		->value('<b>' . $student . '</b>');

	$edit->addControl('Goal', 'protected')
		->value($goal_bench['goal_text']);

	$edit->addControl('Benchmark', 'protected')
		->value($goal_bench['bench_text']);

	$edit->addGroup('Benchmark Assessment Information')
		->collapsed(true);
	$edit->addControl('Benchmark Assessment Description', 'protected')
		->sqlField('assessment_desc');

	$edit->addGroup('Term Length of Benchmark')
		->collapsed(true);
	$edit->addControl('Start Date', 'protected')
		->sqlField('start_date');

	$edit->addControl('End Date', 'protected')
		->sqlField('end_date');

	$edit->addGroup('Benchmark Successful Goal Requirement')
		->collapsed(true);
	$edit->addControl('Required # of Trials per Assessment', 'protected')
		->sqlField('num_trial');

	$edit->addControl('Measurement Quantity Unit of Measure', 'protected')
		->sqlField('unit_of_measure')
		->displayData(
			array(
				'P' => '%',
				'F' => 'Fixed Amount'
			)
		);

	$edit->addControl('Measurement Quantity to be achieved', 'protected')
		->sqlField('quantity_achived');

	$edit->addControl('Measurement Time Period Type', 'protected')
		->sqlField('period_type');

	$edit->addControl('Measurement Time Period #', 'protected')
		->sqlField('period_num');

	$edit->addControl('Trial Description', 'protected')
		->sqlField('trial_desc');

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

	$edit->firstCellWidth = '25%';

	$edit->printEdit();

	$if_indicator_entered = db::execSQL("
		SELECT ind_refid
		  FROM webset.std_bgb_indicator
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->getOne();

	$if_measurement_entered = db::execSQL("
		SELECT m_refid
		  FROM webset.std_bgb_measurement
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	")
	->getOne();

	if ($if_indicator_entered == '' || $if_measurement_entered == '') {
		die(UIMessage::factory('Setup Benchmark Assessments Indicators and Measurement Items first', UIMessage::NOTE)->toHTML());
	}

	$trial = db::execSQL("
		SELECT en.en_refid,
			   en.as_refid,
			   trial_num,
			   trial_date,
			   used_for_baseline,
			   is_met
		  FROM webset.std_bgb_assessment_entry AS en
		 WHERE en.as_refid = $as_refid
		   AND vndrefid = VNDREFID
		 ORDER BY trial_num
	")
	->assocAll();

	$data = array();
	foreach ($trial as $key => $value) {
		$pie_chart = FFButton::factory('% of items meeting Mastery')
			->toolBarView()
			->leftIcon('analysis_16.png')
			->onClick("callPieChart(" . $value['en_refid'] . ", " . $value['as_refid'] . "); api.event.cancel(event);")
			->css('float', 'center');

		$bench = db::execSQL("
			SELECT m.desc_measure,
			       ind.ind_symbol,
			       ind.met_mastery
			  FROM webset.std_bgb_assessment_entry AS en
				   INNER JOIN webset.std_bgb_measurement_benchmark AS mb ON mb.en_refid = en.en_refid
			       INNER JOIN webset.std_bgb_measurement_indicator AS mi ON mi.mi_refid = mb.mi_refid
                   INNER JOIN webset.std_bgb_measurement AS m ON m.m_refid = mi.m_refid
			       INNER JOIN webset.std_bgb_indicator AS ind ON ind.ind_refid = mi.ind_refid
			 WHERE en.as_refid = $as_refid
			   AND trial_num = " . $value['trial_num']
		)
		->assocAll();

		$data[] = array($value['en_refid'], 'Trial #' . $value['trial_num'], 'Date/Time', $value['trial_date'] ? $value['trial_date'] : '');
		$data[] = array($value['en_refid'], 'Trial #' . $value['trial_num'], 'Used for Baseline', $value['used_for_baseline'] == 'Y' ? 'Yes' : 'No');
		foreach ($bench as $key1 => $value1) {
			$symbol_css = array(
				'font-weight' => 'bold',
				'font-size' => '14pt',
				'color' => ($value1['met_mastery'] == 'Y' ? '#00A700' : '#FF0000')
			);
			$ind_symbol = UICustomHTML::factory()
				->append($value1['ind_symbol'])
				->css($symbol_css)
				->toHTML();
			$data[] = array($value['en_refid'], 'Trial #' . $value['trial_num'], $value1['desc_measure'], $ind_symbol);
		}
		$edit_but = FFButton::factory('Edit Trial')
			->hint('Edit Trial')
			->onClick('api.goto("' .
	            CoreUtils::getURL(
	            	'./bgb_assessment_entry_edit.php',
	            	array(
	            		'benchmark_id' => $benchmark_id,
	            		'goal_id' => $goal_id,
	            		'dskey' => $dskey,
	            		'as_refid' => $as_refid,
	            		'RefID' => $value['en_refid']
	            	)
	            ) .
	            '"); api.event.cancel(event);'
	        );

	    $delete_trial = FFButton::factory('Delete Trial')
	    	->hint('Delete Trial')
	    	->onClick('deleteTrial(' . $value['en_refid'] . '); api.event.cancel(event);');

	    $met_mastery = db::execSQL("
			SELECT ind.met_mastery,
				   COUNT(ind.met_mastery)
			  FROM webset.std_bgb_measurement_benchmark AS mb
				   INNER JOIN webset.std_bgb_measurement_indicator AS mi ON mi.mi_refid = mb.mi_refid
			       INNER JOIN webset.std_bgb_indicator AS ind ON ind.ind_refid = mi.ind_refid
			       INNER JOIN webset.std_bgb_measurement AS m ON m.m_refid = mi.m_refid
			 " . ($value['en_refid'] != -1 ? "WHERE mb.en_refid = " . $value['en_refid'] : "" ) . "
			   AND ind.vndrefid = VNDREFID
			   AND m.type_measure = 'Measurable'
			 GROUP BY ind.met_mastery
		")
		->keyedCol();

		$met_mastery['Y'] = array_key_exists('Y', $met_mastery) ? $met_mastery['Y'] : 0;
		$met_mastery['N'] = array_key_exists('N', $met_mastery) ? $met_mastery['N'] : 0;

		$met_mastery_pc = 0;
		if (($met_mastery['Y'] + $met_mastery['N']) != 0) {
			$met_mastery_pc = round ($met_mastery['Y'] * 100 / ($met_mastery['Y'] + $met_mastery['N']), 2);
		}
	    $layout = UILayout::factory()
	    	->addHTML($met_mastery_pc . '%')
	    	->addObject($pie_chart)
	    	->addObject($edit_but)
	    	->addObject($delete_trial)
	    	->addHTML('', '100%')
	    	->toHTML();
		$data[] = array($value['en_refid'], 'Trial #' . $value['trial_num'], 'M %', $layout);
		$data[] = array($value['en_refid'], 'Trial #' . $value['trial_num'], 'Trial used to determine that Goal was Met', $value['is_met'] == 'Y' ? 'Yes' : 'No');
	}

	$pie_chart = FFButton::factory('All Trials Summary')
		->toolBarView()
		->leftIcon('analysis_16.png')
		->onClick("callPieChart(-1, $as_refid); api.event.cancel(event);")
		->css('float', 'center');

	$analyze_func = FFButton::factory('Analyze Benchmark Goal')
		->toolBarView()
		->leftIcon('results1_16.png')
		->onClick("callAnalyze($benchmark_id); api.event.cancel(event);")
		->css('float', 'center');

	$layout = UILayout::factory()
	    ->addObject($pie_chart)
	    ->addObject($analyze_func)
	    ->addHTML('', '90%')
	    ->toHTML();

	if (!empty($trial)) {
		$data[] = array(null, 'Measurement Summary Information', 'All Trials Summary', $layout);
	}

	$list = new ListClass('Trial');

	$list->title = 'Benchmark Measurement';

	$list->hideNumberColumn = true;

	$list->addColumn('', '', 'group');
	$list->addColumn('', '20%');
	$list->addColumn('', '');
	
	$list->fillData($data);

	$list->addURL = CoreUtils::getURL(
		'./bgb_assessment_entry_add.php',
		array(
		    'benchmark_id' => $benchmark_id,
		    'goal_id' => $goal_id,
		    'dskey' => $dskey,
		    'as_refid' => $as_refid,
		    'num_trial' => $num_trial
		)
	);

	$list->pageCount = 300;
	$list->getButton(ListClassButton::ADD_NEW)
		->value('Add Trial');

	$list->printList();
?>
<script type="text/javascript">
	function deleteTrial(en_refid) {
		api.confirm('Do you really want to Delete selected records?',
			function() {
				var post = {}
				post.en_refid = en_refid;
				api.ajax.post(
					api.url('./bgb_assessment_entry_list_delete.ajax.php'),
					post,
					function () {
						api.reload();
					}
				)
			}
		);
	}

	function callPieChart(en_refid, as_refid) {
		api.desktop.open(
			'Measurement Summary Information',
			api.url(
				'./bgb_assessment_entry_sum_information.php',
				{'en_refid' : en_refid, 'as_refid' : as_refid}
			)
		).maximize().show();
	}

	function callAnalyze(brefid) {
		api.desktop.open(
			'Analyze Benchmark Goal',
			api.url(
				'./bgb_assessment_entry_analyze_goal.php',
				{'brefid' : brefid}
			)
		).maximize().show();
	}
</script>