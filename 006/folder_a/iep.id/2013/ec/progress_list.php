<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = IDEAStudent::factory($tsRefID);
	$siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');

	$iepyear = IDEAStudentIEPYear::factory($siymrefid);

	$progress = db::execSQL("
        SELECT refid as sprrefid,
               int01 as stdgoalrefid,
               int02 as stdbenchmarkrefid,
               txt01 as narrative,
               int04 as dsyrefid,
               int05 as sprmarkingprd,
               int06 as percent,
               epsdesc
          FROM webset.std_general std
               INNER JOIN webset.disdef_progressrepext ext ON std.int03 = ext.eprefid
         WHERE stdrefid = " . $tsRefID . "
           AND area_id = " . IDEAAppArea::ID_EC_PROGRESS . "
         ORDER BY 2, 3, 5, 6
    ")->assocAll();

	$periods = IDEASchool::factory($student->get('vourefid'))
		->getMarkingPeriods($iepyear->get(IDEAStudentIEPYear::F_BEG_DATE), $iepyear->get(IDEAStudentIEPYear::F_END_DATE));

	$iepyears = FFSelect::factory('IEP Year')
		->name('siymrefid')
		->value($siymrefid)
		->sql("
            SELECT siymrefid,
                   TO_CHAR(siymiepbegdate, 'MM/DD/YYYY') || ' - ' || TO_CHAR(siymiependdate, 'MM/DD/YYYY')
              FROM webset.std_iep_year
             WHERE stdrefid = " . $tsRefID . "
             ORDER BY siymiepbegdate DESC
        ")
		->onChange("api.goto(api.url('progress_list.php', {'dskey' : '" . $dskey . "', 'siymrefid' : $('#siymrefid').val()}))");

	$list = new ListClass();
	$list->title = 'Early Childhood Goals Progress Report';
	$list->hideCheckBoxes = true;
	$list->hideNumberColumn = true;

	$list->SQL = "
       SELECT * FROM (SELECT g.refid as grefid,
                             validvalueid AS outcome,
                             g.txt02 AS gsentance,
                             NULL AS bsentance,
                             NULL AS orefid,
                             g.order_num AS g_num,
                             NULL AS b_num,
                             g.iepyear,
                             outcome.order_num as out_num
                        FROM webset.std_general g
			                 INNER JOIN webset.std_general outcome ON outcome.refid = g.int01
			                 INNER JOIN webset.glb_validvalues def ON outcome.int01 = def.refid
                       WHERE g.stdrefid = " . $tsRefID . "
                         AND g.iepyear = " . $siymrefid . "
                         AND g.area_id = " . IDEAAppArea::ID_EC_GOALS . "
                         AND outcome.int10 = 1
                       UNION ALL
                      SELECT g.refid as grefid,
                             validvalueid AS outcome,
                             NULL AS gsentance,
                             o.txt01 AS bsentance,
                             o.refid as orefid,
                             g.order_num AS g_num,
                             o.order_num AS b_num,
                             g.iepyear,
                             outcome.order_num as out_num
                        FROM webset.std_general g
			                 INNER JOIN webset.std_general outcome ON outcome.refid = g.int01
			                 INNER JOIN webset.glb_validvalues def ON outcome.int01 = def.refid
			                 INNER JOIN webset.std_general o ON g.refid = o.int01 AND o.area_id = " . IDEAAppArea::ID_EC_OBJECTIVES . "
                       WHERE g.stdrefid = " . $tsRefID . "
                         AND g.iepyear = " . $siymrefid . "
                         AND g.area_id = " . IDEAAppArea::ID_EC_GOALS . "
                         AND outcome.int10 = 1
                     ) as t
        ORDER BY out_num, COALESCE(g_num, 0), COALESCE(grefid, 0), COALESCE(b_num, 0), COALESCE(orefid, 0)";

	$list->addColumn('Outcome');
	$list->addColumn('Goal/Objective')
		->dataCallback('markGoalsObjectives');

	for ($i = 1; $i <= count($periods); $i++) {
		$rep_line[] = $periods[$i]['bm'] . ' / ' . $periods[$i]['dsydesc'];
		$list->addColumn($periods[$i]['bm'] . ' / ' . $periods[$i]['dsydesc'])
			->align('center')
			->dataCallback('showProgressMark');
	}


	$print_ds = DataStorage::factory()
		->set('progress', $progress)
		->set('goal_objectives', db::execSQL($list->SQL)->assocAll())
		->set('periods', $periods);

	$print_button = FFPrintButton::factory(CoreUtils::getURL('progress_print.ajax.php', array('dskey' => $dskey, 'print_dskey' => $print_ds->getKey(), 'siymrefid' => $siymrefid)));

	$panel = UILayout::factory()
		->addHTML('', '90%')
		->addObject($print_button, 'right')
		->addHTML('', '20px')
		->addHTML($iepyears->toHTML());

	$list->addHTML($panel->toHTML(), ListClassElement::CONTROL_PANEL_RIGHT);

	$list->printList();

	print FormField::factory('hidden')->name('dskey')->value($dskey)->toHTML();

	function markGoalsObjectives($data, $col) {
		if ($data['gsentance'] == '') {
			return UILayout::factory()
				->addHTML('', '5%')
				->addHTML($data['g_num'] . '.' . $data['b_num'] . ' ' . $data['bsentance'], '[color:blue; font-weight: bold;]')
				->toHTML();
		} else {
			return UILayout::factory()
				->addHTML($data['g_num'] . ' ' . $data['gsentance'], '[color:brown; font-weight: bold;]')
				->toHTML();
		}
	}

	function showProgressMark($data, $col) {
		global $progress;
		global $periods;
		$col = $col - 2;
		if ($data['gsentance'] == '') {
			for ($i = 0; $i < count($progress); $i++) {
				if ($progress[$i]['dsyrefid'] == $periods[$col]['dsyrefid'] &&
					$progress[$i]['sprmarkingprd'] == $periods[$col]['bmnum'] &&
					$progress[$i]['stdgoalrefid'] == $data['grefid'] &&
					$progress[$i]['stdbenchmarkrefid'] == $data['orefid']
				) {
					return FFMenuButton::factory($progress[$i]["epsdesc"])
						->addItem('Edit', 'editProgress(' . $progress[$i]['sprrefid'] . ', ' . $data['orefid'] . ')')
						->addItem('Delete', 'deleteProgress(' . $progress[$i]['sprrefid'] . ')')
						->width('50%')
						->toHTML() . $progress[$i]["narrative"];
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
						->toHTML() . $progress[$i]["narrative"];
				}
			}
		}
		return FFMenuButton::factory('Empty')
			->addItem('Add', 'addProgress(' . $periods[$col]['bmnum'] . ', ' . $data['grefid'] . ', ' . (int)$data['orefid'] . ', ' . $periods[$col]['dsyrefid'] . ')')
			->width('50%')
			->css('font-style', 'italic')
			->css('font-weight', 'normal')
			->toHTML();
	}

?>
<script type="text/javascript">
	function addProgress(period, grefid, orefid, dsyrefid) {
		api.goto(
			'progress_add.php',
			{
				'dskey': $("#dskey").val(),
				'siymrefid': $("#siymrefid").val(),
				'period': period,
				'grefid': grefid,
				'orefid': orefid,
				'dsyrefid': dsyrefid
			}
		);

	}
	UIP
	function editProgress(sprrefid, orefid) {
		api.goto(
			'progress_add.php',
			{
				'dskey': $("#dskey").val(),
				'siymrefid': $("#siymrefid").val(),
				'sprrefid': sprrefid,
				'orefid': orefid
			}
		);
	}

	function deleteProgress(sprrefid) {
		api.goto(
			'progress_delete.php',
			{
				'dskey': $("#dskey").val(),
				'siymrefid': $("#siymrefid").val(),
				'sprrefid': sprrefid
			}
		);
	}
</script>
