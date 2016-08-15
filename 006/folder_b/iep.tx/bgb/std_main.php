<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stdfirstname = $ds->safeGet('stdfirstname');
	$esy = io::get('ESY');
	$goal_statement = str_replace('stdfirstname', $stdfirstname, IDEAPartsTX::get('goal_statement'));
	$objective_statement = str_replace('stdfirstname', $stdfirstname, IDEAPartsTX::get('objective_statement'));

	#Goal List
	$list1 = new ListClass('goal');
	$list1->title = ($esy == 'Y' ? 'ESY ' : '') . 'Goals';
	$list1->multipleEdit = false;
	$list1->hideNumberColumn = true;

	$list1->SQL = "
		SELECT grefid,
			   order_num,
			   CASE WHEN subject='Other' THEN othersub ELSE subject END,
			   " . $goal_statement . ",
			   'View Objectives (' || (SELECT count(1) FROM webset_tx.std_sb_objectives o WHERE o.grefid=g.grefid)  || ')' as gview
		  FROM webset_tx.std_sb_goals g
			   INNER JOIN webset_tx.def_sb_subjects s ON s.subrefid = g.subrefid
			   INNER JOIN webset_tx.def_sb_action ga ON ga.arefid = g.action_id
			   INNER JOIN webset.glb_validvalues gv ON gv.refid = g.timeframe_id
			   INNER JOIN webset_tx.def_sb_criteria gc ON gc.ctrefid = g.criteria_id
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
		   AND esy = '" . $esy . "'
		 ORDER BY COALESCE(order_num, grefid)
    ";

	$list1->addColumn('Order #', '5%')->dataCallback('markCurGoal');
	$list1->addColumn('Subject', '10%')->dataCallback('markCurGoal');
	$list1->addColumn('Goal', '70%')->dataCallback('markCurGoal');	
	$list1->addColumn('View Goals', '15%')
		->type('link')
		->param('javascript:api.goto("' .
			CoreUtils::getURL('', array('goal_id' => 'AF_REFID')) .
			'");')
		->dataCallback('markCurGoal');

	$list1->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sb_goals')
			->setKeyField('grefid')
			->applyListClassMode('goal')
	);

	$list1->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list1->addRecordsProcess('Delete')
		->message('Do you really want to delete selected Goals?')
		->url(CoreUtils::getURL('std_delete.ajax.php', array_merge($_GET, array('mode' => 'goal'))))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list1->addURL = CoreUtils::getURL('std_goal_add.php', $_GET);
	$list1->editURL = CoreUtils::getURL('std_goal_add.php', $_GET);

	#Goal List
	if (io::geti('goal_id') > 0) {
		$goal_id = io::geti('goal_id');
	} else {
		$goal_id = (int) db::execSQL($list1->SQL)->getOne();
	}
	$goal = db::execSQL("
		SELECT *
		  FROM webset_tx.std_sb_goals
		 WHERE stdrefid = " . $tsRefID . "
		   AND grefid = " . $goal_id . "
     ")->assoc();
	
	if ($goal['grefid'] > 0) {
		$goalTitle = 'Goal #' . $goal['order_num'];
	} else {
		$goal_id = 0;
		$goalTitle = '';
	}

	$list2 = new ListClass('objective');
	$list2->title = ($esy == 'Y' ? 'ESY ' : '') . $goalTitle . ' Objectives';
	$list2->multipleEdit = false;
	$list2->hideNumberColumn = true;

	$list2->SQL = "
		SELECT orefid,
			   order_num,
			   " . $objective_statement . ",
		       order_num
		  FROM webset_tx.std_sb_objectives o
			   INNER JOIN webset_tx.def_sb_action oa ON oa.arefid = o.action_id
			   INNER JOIN webset.glb_validvalues ov ON ov.refid = o.timeframe_id
			   INNER JOIN webset_tx.def_sb_criteria oc ON oc.ctrefid = o.criteria_id
		 WHERE grefid = " . $goal_id . "
		 ORDER BY COALESCE(order_num, orefid)
    ";

	$list2->addColumn('Order', '5%');
	$list2->addColumn('Objective', '95%');

	$list2->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sb_objectives')
			->setKeyField('orefid')
			->applyListClassMode('objective')
	);

	$list2->addRecordsProcess('Delete')
		->message('Do you really want to delete selected Objectives?')
		->url(CoreUtils::getURL('std_delete.ajax.php', array_merge($_GET, array('mode' => 'objective'))))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false)
		->disabled($goal_id == 0);

	$list2->getButton(ListClassButton::ADD_NEW)->disabled($goal_id == 0);

	$list2->addURL = CoreUtils::getURL('std_objective_add.php', array_merge($_GET, array('goal_id' => $goal_id)));
	$list2->editURL = CoreUtils::getURL('std_objective_add.php', array_merge($_GET, array('goal_id' => $goal_id)));

	print UIFrameSet::factory('100%', '50%, 50%')
			->addFrame(
				UIFrame::factory()
				->addObject($list1)
			)
			->addFrame(
				UIFrame::factory()
				->addObject($list2)
			)
			->toHTML();

	function markCurGoal($data, $col) {
		global $goal_id;
		if ($data['grefid'] == $goal_id) {
			return UILayout::factory()
					->addHTML($data[$col], '[font-weight: bold;]')
					->toHTML();
		} else {
			return $data[$col];
		}
	}

?>