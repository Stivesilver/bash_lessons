<?php

    Security::init();

	$dskey     = io::get('dskey');
	$ds        = DataStorage::factory($dskey);
	$tsRefID   = $ds->safeGet('tsRefID');
	$siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
	$list      = new ListClass();
	$esy       = io::get('esy');
	$student   = IDEAStudent::factory($tsRefID);
	$school    = IDEASchool::factory($student->get('vourefid'));

	$list->title = 'Student Progress Reports';

	if ($esy == '') {
		$esy = 'N';
	}

	$iepyear = db::execSQL("
        SELECT *
          FROM webset.std_iep_year
         WHERE siymrefid = $siymrefid
    ")->assoc();

	$periods  = $school->getMarkingPeriods($iepyear['siymiepbegdate'], $iepyear['siymiependdate'], $esy);

	$progress = db::execSQL("
        SELECT sprrefid,
               stdgoalrefid,
               stdbenchmarkrefid,
               sprnarative,
               dsyrefid,
               sprmarkingprd,
               percentofprogress,
               epsdesc
          FROM webset.std_progressreportmst std
               INNER JOIN webset.disdef_progressrepext ext ON std.eprefid = ext.eprefid
         WHERE stdrefid = " . $tsRefID . "
         ORDER BY stdgoalrefid, stdbenchmarkrefid, dsyrefid, sprmarkingprd
    ")->assocAll();

	$iepyears = FFSelect::factory('IEP Year')
		->name('siymrefid')
		->value($siymrefid)
		->sql("
            SELECT siymrefid,
                   TO_CHAR(siymiepbegdate, 'MM/DD/YYYY') || ' - ' || TO_CHAR(siymiependdate, 'MM/DD/YYYY')
              FROM webset.std_iep_year
             WHERE stdrefid = $tsRefID
             ORDER BY siymiepbegdate DESC
        ")
		->onChange("api.goto(api.url('goals.php', {'dskey' : '" . $dskey . "', 'ESY' : '" . $esy . "', 'siymrefid' : this.value}))");

	$printButton = FFButton::factory('Print')
		->leftIcon('./img/printer.png')
		->onClick('api.ajax.process(ProcessType.REPORT, "' .
				  CoreUtils::getURL(
					  '../../../iep/documentation/pr_progrep_print.ajax.php',
					  array(
						  'dskey'     => $dskey,
						  'tsRefID'   => $tsRefID,
						  'siymrefid' => $siymrefid
					  )
				  ) . '")'
		);

	$panel = UILayout::factory()
		->addHTML('', '90%')
		->addObject($printButton, 'right')
		->addHTML('', '20px')
		->addObject($iepyears, 'left');

	$list->SQL = "
       SELECT *
         FROM (SELECT goal.blrefid,
	                  grefid,
	                  COALESCE(gsentance, overridetext) AS gsentance,
	                  NULL AS bsentance,
	                  percentofprogress,
	                  NULL AS brefid,
	                  baseline.order_num AS bl_num,
	                  goal.order_num AS g_num,
	                  NULL AS b_num,
	                  siymrefid
	             FROM webset.std_bgb_goal goal
	                  INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
	            WHERE goal.stdrefid = " . $tsRefID . "
	                  AND baseline.siymrefid = " . $siymrefid . "
	                  AND baseline.esy = '" . $esy . "'
	            UNION ALL
	           SELECT goal.blrefid,
	                  goal.grefid,
	                  NULL AS gsentance,
	                  COALESCE(bsentance, benchmark.overridetext) AS bsentance,
	                  benchmark.percentofprogress,
	                  brefid,
	                  baseline.order_num AS bl_num,
	                  goal.order_num AS g_num,
	                  benchmark.order_num AS b_num,
	                  siymrefid
	             FROM webset.std_bgb_goal goal
	                  INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
	                  INNER JOIN webset.std_bgb_benchmark benchmark ON goal.grefid = benchmark.grefid
	            WHERE goal.stdrefid = " . $tsRefID . "
	                  AND baseline.siymrefid = " . $siymrefid . "
	                  AND baseline.esy = '" . $esy . "'
               ) as t
       ORDER BY COALESCE(bl_num, 0), COALESCE(blrefid, 0), COALESCE(g_num, 0), COALESCE(grefid, 0), COALESCE(b_num, 0), COALESCE(brefid, 0)
       ";

	$list->addColumn('Goal/Benchmark')
		->dataCallback('benchmarks');

	foreach ($periods as $period) {
		$list->addColumn($period['bm'])
		->align('center')
		->dataCallback('showProgressMark');
	}

	$list->addHTML($panel->toHTML(), ListClassElement::CONTROL_PANEL_RIGHT);

	$list->printList();

	function benchmarks($data) {
		if ($data['gsentance'] == '') {
			return UILayout::factory()
				->addHTML('', '5%')
				->addHTML($data['bsentance'], '[color:blue; font-weight: bold;]')
				->toHTML();
		} else {
			return UILayout::factory()
				->addHTML($data['gsentance'], '[color:brown; font-weight: bold;]')
				->toHTML();
		}

	}

	function showProgressMark($data, $col) {
		global $progress;
		global $periods;
		$col = $col - 1;
		if ($data['gsentance'] == '') {
			for ($i = 0; $i < count($progress); $i++) {
				if ($progress[$i]['dsyrefid'] == $periods[$col]['dsyrefid'] &&
					$progress[$i]['sprmarkingprd'] == $periods[$col]['bmnum'] &&
					$progress[$i]['stdgoalrefid'] == $data['grefid'] &&
					$progress[$i]['stdbenchmarkrefid'] == $data['brefid']
				) {
					return FFMenuButton::factory($progress[$i]["epsdesc"])
						->addItem('Edit', 'editProgress(' . $progress[$i]['sprrefid'] . ', ' . $data['brefid'] . ')')
						->addItem('Delete', 'deleteProgress(' . $progress[$i]['sprrefid'] . ')')
						->width('50%')
						->toHTML();
				}
			}
		} else {
			for ($i = 0; $i < count($progress); $i++) {
				if ($progress[$i]['dsyrefid'] == $periods[$col]['dsyrefid'] &&
					$progress[$i]['sprmarkingprd'] == $periods[$col]['bmnum'] &&
					$progress[$i]['stdgoalrefid'] == $data['grefid'] &&
					$progress[$i]['stdbenchmarkrefid'] == ''
				) {
					return FFMenuButton::factory($progress[$i]["epsdesc"])
						->addItem('Edit', 'editProgress(' . $progress[$i]['sprrefid'] . ', 0)')
						->addItem('Delete', 'deleteProgress(' . $progress[$i]['sprrefid'] . ')')
						->width('50%')
						->toHTML();
				}
			}
		}
		return FFButton::factory('', 'addProgress(' . $periods[$col]['bmnum'] . ', ' . $data['grefid'] . ', ' . (int) $data['brefid'] . ', ' . $periods[$col]['dsyrefid'] . ')')
			->width('50%')
			->toHTML();
	}

	io::jsVar('dskey',     $dskey);
	io::jsVar('esy',       $esy);
	io::jsVar('siymrefid', $siymrefid);

?>

<script type="text/javascript">

	function addProgress(period, grefid, brefid, dsyrefid) {
		api.goto(
			'../../../iep/documentation/pr_progrep_add.php',
			{
				'dskey': dskey,
				'ESY': esy,
				'siymrefid': siymrefid,
				'period': period,
				'grefid': grefid,
				'brefid': brefid,
				'dsyrefid': dsyrefid
			}
		);

	}

	function editProgress(sprrefid, brefid) {
		api.goto(
			'../../../iep/documentation/pr_progrep_add.php',
			{
				'dskey': dskey,
				'ESY': esy,
				'siymrefid': siymrefid,
				'sprrefid': sprrefid,
				'brefid': brefid
			}
		);
	}

	function deleteProgress(sprrefid) {
		api.goto(
			'../../../iep/documentation/pr_progrep_add.php',
			{
				'dskey': dskey,
				'ESY': esy,
				'siymrefid': siymrefid,
				'sprrefid': sprrefid
			}
		);
	}

</script>
