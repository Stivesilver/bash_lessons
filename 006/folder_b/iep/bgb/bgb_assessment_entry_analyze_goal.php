<?php

	Security::init();

	$brefid = io::geti('brefid');

	IDEAStudentBenchmarkAssessment::factory($brefid)
		->analyzeBenchmarkGoal();

	$list = new ListClass('list');

	$list->SQL = "
		SELECT last_analysis,
			   last_data_collection,
			   CASE
			   		WHEN goal_status = 'M'
			   		THEN 'Goal Met'
			   		WHEN goal_status = 'N'
			   		THEN 'Goal Not Met'
			   END goal_status,
			   number_data_collection,
			   number_data_collection_req_left,
			   number_data_collection_req_success,
			   assessment_desc
		  FROM webset.std_bgb_current_status AS cs
		  	   INNER JOIN webset.std_bgb_assessment as sba ON sba.brefid = cs.brefid
		 WHERE cs.brefid = $brefid
		   AND cs.vndrefid = VNDREFID
	";

	$list->title = 'Analyze Benchmark Goal';

	$list->addColumn('Assessment Description')
		->sqlField('assessment_desc');

	$list->addColumn('Last Analysis', '15%', 'datetime')
		->sqlField('last_analysis');

	$list->addColumn('Last Data Collection', '15%', 'datetime')
		->sqlField('last_data_collection');

	$list->addColumn('Goal Status', '10%')
		->sqlField('goal_status');

	$list->addColumn('Data Collections', '10%')
		->sqlField('number_data_collection');

	$list->addColumn('Number of Data Collections', '15%')
		->hint('Number Data Collection Left to meet the minimum')
		->sqlField('number_data_collection_req_left');

	$list->addColumn('Number of Data Collections', '15%')
		->hint('Number Data Collections Left to possibly meet the Goal')
		->sqlField('number_data_collection_req_success');

	$list->printList();
?>