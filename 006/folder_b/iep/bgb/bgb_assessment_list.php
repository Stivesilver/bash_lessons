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

	$edit = new EditClass('edit1', $goal_id);

	$edit->title = 'Goal / Benchmark';
	$edit->SQL = "
		SELECT COALESCE(goal.overridetext, goal.gsentance) AS goal_text,
			   COALESCE(bench.overridetext, bench.bsentance) AS bench_text
		  FROM webset.std_bgb_goal AS goal
			   INNER JOIN webset.std_bgb_benchmark AS bench ON bench.grefid = goal.grefid
		 WHERE goal.grefid = $goal_id
		   AND brefid = $benchmark_id
	";

	$edit->addControl('Goal', 'protected')
		->sqlField('goal_text');

	$edit->addControl('Benchmark', 'protected')
		->sqlField('bench_text');

	$edit->firstCellWidth = '12%';
	$edit->printEdit();

	$count_assessment = db::execSQL("
		SELECT COUNT(as_refid)
		  FROM webset.std_bgb_assessment
		 WHERE brefid = $benchmark_id
		   AND vndrefid = VNDREFID
	")
	->getOne();

	$list = new ListClass('Assessment');

	$list->title = 'Benchmark Assessments';

	$list->SQL = "
		SELECT as_refid,
			   assessment_desc,
			   start_date,
			   end_date
		  FROM webset.std_bgb_assessment
		 WHERE brefid = $benchmark_id
		   AND vndrefid = VNDREFID
	";

	$list->addColumn('Name')
		->sqlField('assessment_desc');

	$list->addColumn('Start Date')
		->sqlField('start_date');

	$list->addColumn('End Date')
		->sqlField('end_date');

	$list->addURL = CoreUtils::getURL('./bgb_assesment_edit.php', array('brefid' => $benchmark_id, 'dskey' => $dskey, 'goal_id' => $goal_id));
	$list->editURL = CoreUtils::getURL('./bgb_assesment_edit.php', array('brefid' => $benchmark_id, 'dskey' => $dskey, 'goal_id' => $goal_id));

	$list->getButton(ListClassButton::ADD_NEW)
		->disabled($count_assessment == 0 ? false : true);

	$list->deleteTableName = 'webset.std_bgb_assessment';
	$list->deleteKeyField = 'as_refid';

	$list->printList();
?>