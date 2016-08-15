<?php

	Security::init();

	$RefID = io::geti('RefID');
	$brefid = io::geti('brefid');
	$goal_id = io::geti('goal_id');

	for ($i = 1; $i < 101; $i++) {
		$array_num[$i] = $i;
	}

	$dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    $edit = new EditClass('edit1', $goal_id);

	$edit->title = 'Goal / Benchmark';
	$edit->SQL = "
		SELECT COALESCE(goal.overridetext, goal.gsentance) AS goal_text,
			   COALESCE(bench.overridetext, bench.bsentance) AS bench_text
		  FROM webset.std_bgb_goal AS goal
			   INNER JOIN webset.std_bgb_benchmark AS bench ON bench.grefid = goal.grefid
		 WHERE goal.grefid = $goal_id
		   AND brefid = $brefid
	";

	$edit->addControl('Goal', 'protected')
		->sqlField('goal_text');

	$edit->addControl('Benchmark', 'protected')
		->sqlField('bench_text');

	$edit->firstCellWidth = '12%';

	$edit->finishURL = '';
	$edit->cancelURL = '';

	$edit->printEdit();

	$student = IDEAStudent::factory($tsRefID);

	$edit = new EditClass('Assessment', $RefID);

	$edit->title = 'Add Benchmark Assessment';

	$edit->setSourceTable('webset.std_bgb_assessment', 'as_refid');

	$edit->addGroup('Description');
	$edit->addControl('Description')
		->sqlField('assessment_desc')
		->width('300px')
		->req(true);

	$edit->addGroup('Term Length of Benchmark');
	$edit->addControl('Start Date', 'date')
		->sqlField('start_date')
		->value($student->getDate('stdiepyearbgdt'));

	$edit->addControl('End Date', 'date')
		->sqlField('end_date')
		->value($student->getDate('stdiepyearendt'));

	$edit->addGroup('Benchmark Successful Goal Requirement');
	
	$edit->addControl('Trial Description')
		->sqlField('trial_desc')
		->width('350px');

	$edit->addControl('Data Collection Method', 'select')
		->sqlField('data_collection_method')
		->value('T')
		->data(
			array(
				'T' => 'Measurements Entered in Total by Measurement Item',
				'C' => 'Measurements Entered by Measurement Item'
			)
		);

	$edit->addControl('Required # of Trials per Data Recording', 'select')
		->sqlField('num_trial')
		->data($array_num)
		->value(3);

	$edit->addControl('Performance Measurement Type', 'select')
		->sqlField('measure_type')
		->value('I')
		->sql("
			SELECT validvalueid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'MO_Bench_Assess_PMT'
			   AND (glb_enddate IS NULL OR now() < glb_enddate)
			 ORDER BY sequence_number, validvalue
		");

	$edit->addControl('Measurement Quantity Unit of Measure', 'select')
		->sqlField('unit_of_measure')
		->data(
			array(
				'P' => '%',
				'F' => 'Fixed Amount'
			)
		)
		->value('%');

	$edit->addControl('Measurement Quantity to be achieved', 'select')
		->sqlField('quantity_achived')
		->data($array_num)
		->value(20);

	$edit->addControl('Measurement Basis', 'select')
		->value(1)
		->sqlField('measurement_basis')
		->name('measurement_basis')
		->data(
			array(
				'1' => 'Based on X Out of Y Consecutive Data Collections',
				'2' => 'Based on all Data Collections',
				'3' => 'Based on # of Data Collections',
				'4' => 'Based on Consecutive Data Collections'
			)
		);

	$edit->addControl('Successful Data Collections Required', 'int')
		->sqlField('successful_data')
		->hideIf('measurement_basis', array('2', '4'));

	$edit->addControl('# of Consecutive Data Collections', 'int')
		->sqlField('num_consec_data')
		->showIf('measurement_basis', array('1', '4'));
/*
	$edit->addControl('Measurement Time Period Type', 'select')
		->sqlField('period_type')
		//->hideIf('measurement_basis', 1)
		->data(array('Days' => 'Days'));

	$edit->addControl('Measurement Time Period #', 'integer')
		//->hideIf('measurement_basis', 1)
		->sqlField('period_num');
*/
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

	$edit->addControl('brefid', 'hidden')
		->sqlField('brefid')
		->value($brefid);

	$edit->firstCellWidth = '25%';

	$edit->saveAndAdd = false;
	$edit->saveAndEdit= true;

	$edit->printEdit();

	if ($RefID != 0) {
		$ui_tabs = new UITabs();

		$ui_tabs->indent(10);
		$ui_tabs->addTab('Indicators', CoreUtils::getURL('./bgb_indicator_list.php', array('as_refid' => $RefID)));
		$ui_tabs->addTab('Measurement Items', CoreUtils::getURL('./bgb_measurement_list.php', array('as_refid' => $RefID)));

		print $ui_tabs->toHTML();
	}
?>