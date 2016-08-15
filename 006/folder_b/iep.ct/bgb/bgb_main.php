<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $esy = io::get('ESY');
	$titles = json_decode(IDEAFormat::getIniOptions('bgb'), true);

    #Baseline List
    $list1 = new ListClass('baseline');
    $list1->title = ($esy == 'Y' ? 'ESY ' : '') . 'Domain - Scope - Key Skill Area';
    $list1->multipleEdit = false;
    $list1->hideNumberColumn = true;

    $list1->SQL = "
        SELECT blrefid,
               std.order_num,
               " . IDEAParts::get('baselineArea') . ",
               'View " . $titles['goals'] . "'::varchar as gview,
               order_num
          FROM webset.std_bgb_baseline std
               INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON std.blksa = ksa.gdskrefid
               INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
               INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
         WHERE stdrefid = " . $tsRefID . "
           AND std.siymrefid = " . $stdIEPYear . "
           AND std.esy = '" . $esy . "'
         ORDER BY std.order_num, blrefid
    ";

    $list1->addColumn('Order', '%')->dataCallback('markCurBaseline');
    $list1->addColumn('Domain - Scope - Key Skill Area', '90%')->dataCallback('markCurBaseline');
    $list1->addColumn('View Goals', '5%')
        ->type('link')
        ->param('javascript:api.goto("' .
            CoreUtils::getURL('', array('baseline_id' => 'AF_REFID', 'goal_id' => null)) .
            '");');

    $list1->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_bgb_baseline')
            ->setKeyField('blrefid')
            ->applyListClassMode('baseline')
    );

    $list1->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

	$list1->addButton(
		FFIDEAActionButton::factory()
			->setSeqTable('webset.std_bgb_baseline', 'blrefid', 'order_num')
			->key('stdrefid', $tsRefID)
			->key('siymrefid', $stdIEPYear)
			->key('esy', $esy)
			->setNestingSeq('webset.std_bgb_goal', 'grefid', 'order_num', 'blrefid', 'webset.std_bgb_baseline', 'blrefid')
			->setNestingSeq('webset.std_bgb_benchmark', 'brefid', 'order_num', 'grefid', 'webset.std_bgb_goal', 'grefid')
			->reorderSeq()
	);

    $list1->addRecordsProcess('Delete')
        ->message('Do you really want to delete selected Baselines?')
        ->url(CoreUtils::getURL('bgb_delete.ajax.php', array_merge($_GET, array('mode' => 'baseline'))))
        ->type(ListClassProcess::DATA_UPDATE)
	    ->width('80px')
        ->progressBar(false);

    $list1->addURL = CoreUtils::getURL('bgb_baseline_add.php', $_GET);
    $list1->editURL = CoreUtils::getURL('bgb_baseline_add.php', $_GET);

	#Goal List
    if (io::geti('baseline_id') > 0 && db::execSQL("
                                         SELECT 1
                                           FROM webset.std_bgb_baseline
                                          WHERE stdrefid = " . $tsRefID . "
                                            AND blrefid = " . io::geti('baseline_id') . "
                                        ")->getOne() == '1') {
        $baseline_id = io::geti('baseline_id');
    } else {
        $baseline_id = (int) db::execSQL($list1->SQL)->getOne();
    }

    $list2 = new ListClass('goal');
    $list2->title = ($esy == 'Y' ? 'ESY ' : '') . 'Goals';
    $list2->multipleEdit = false;
    $list2->hideNumberColumn = true;

    $list2->SQL = "
        SELECT grefid,
               order_num,
               COALESCE(overridetext,gsentance),
               'View " . $titles['benchmarks'] . "'::varchar as bview,
               order_num
          FROM webset.std_bgb_goal
         WHERE blrefid = " . $baseline_id . "
         ORDER BY order_num, grefid
    ";

    $list2->addColumn('Order', '5%')->dataCallback('markCurGoal');
    $list2->addColumn('Goal', '90%')->dataCallback('markCurGoal');
    $list2->addColumn('View Objectives', '5%')
        ->type('link')
	    ->sqlField('bview')
        ->param('javascript:api.goto("' .
            CoreUtils::getURL('', array('baseline_id' => $baseline_id, 'goal_id' => 'AF_REFID')) .
            '");');

    $list2->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_bgb_goal')
            ->setKeyField('grefid')
            ->applyListClassMode('goal')
    );

    $list2->addRecordsProcess('Delete')
        ->message('Do you really want to delete selected Goals?')
        ->url(CoreUtils::getURL('bgb_delete.ajax.php', array_merge($_GET, array('mode' => 'goal'))))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false)
        ->disabled($baseline_id == 0);

    $list2->getButton(ListClassButton::ADD_NEW)->disabled($baseline_id == 0);

    $list2->addURL = CoreUtils::getURL('bgb_goal_add.php', array_merge($_GET, array('baseline_id' => $baseline_id)));
    $list2->editURL = CoreUtils::getURL('bgb_goal_add.php', array_merge($_GET, array('baseline_id' => $baseline_id)));

    #Benachmark List
    if (io::geti('goal_id') > 0 && db::execSQL("
                                         SELECT 1
                                           FROM webset.std_bgb_goal
                                          WHERE stdrefid = " . $tsRefID . "
                                            AND grefid = " . io::geti('goal_id') . "
                                        ")->getOne() == '1') {
        $goal_id = io::geti('goal_id');
    } else {
        $goal_id = (int) db::execSQL($list2->SQL)->getOne();
    }

    $list3 = new ListClass('benchmark');
    $list3->title = ($esy == 'Y' ? 'ESY ' : '') . 'Objectives';
    $list3->multipleEdit = false;
    $list3->hideNumberColumn = true;

    $list3->SQL = "
        SELECT brefid,
               order_num,
               COALESCE(overridetext, bsentance),
               order_num
          FROM webset.std_bgb_benchmark
         WHERE grefid = " . $goal_id . "
         ORDER BY order_num, brefid
    ";

    $list3->addColumn('Order', '5%');
    $list3->addColumn('Objective', '95%');

    $list3->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_bgb_benchmark')
            ->setKeyField('brefid')
            ->applyListClassMode('benchmark')
    );

    $list3->addRecordsProcess('Delete')
        ->message('Do you really want to delete selected Goals?')
        ->url(CoreUtils::getURL('bgb_delete.ajax.php', array_merge($_GET, array('mode' => 'benchmark'))))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false)
        ->disabled($goal_id == 0);

    $list3->getButton(ListClassButton::ADD_NEW)->disabled($goal_id == 0);

    $list3->addURL = CoreUtils::getURL('bgb_benchmark_add.php', array_merge($_GET, array('baseline_id' => $baseline_id, 'goal_id' => $goal_id)));
    $list3->editURL = CoreUtils::getURL('bgb_benchmark_add.php', array_merge($_GET, array('goal_id' => $goal_id)));

	$finalHTML = UILayout::factory()
		->newLine('[height: 33%;]')
		->addObject($list1, 'top')
		->newLine('[height: 33%;]')
		->addObject($list2, 'top')
		->newLine('[height: 33%;]')
		->addObject($list3, 'top');

	print $finalHTML->toHTML();

    function markCurBaseline($data, $col) {
        global $baseline_id;
        if ($data['blrefid'] == $baseline_id) {
            return UILayout::factory()
                    ->addHTML($data[$col], '[font-weight: bold;]')
                    ->toHTML();
        } else {
            return $data[$col];
        }
    }

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
