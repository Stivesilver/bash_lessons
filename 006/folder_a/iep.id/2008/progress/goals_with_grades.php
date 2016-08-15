<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = IDEAStudent::factory($tsRefID);
	$siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');

	$iepyear = IDEAStudentIEPYear::factory($siymrefid);

	$progress = db::execSQL("
        SELECT sprrefid,
               stdgoalrefid,
               stdbenchmarkrefid,
               std.sprnarative,
               dsyrefid,
               sprmarkingprd,
               std.percentofprogress,
               epsdesc
          FROM webset.std_oth_progress std
               INNER JOIN webset.disdef_progressrepext ext ON std.eprefid = ext.eprefid
         WHERE stdrefid = $tsRefID
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
		->onChange("api.goto(api.url('goals_with_grades.php', {'dskey' : '" . $dskey . "', 'siymrefid' : $('#siymrefid').val()}))");

	$list = new ListClass();
	$list->title = 'IEP Goals Progress Reports';
	$list->hideCheckBoxes = true;
	$list->hideNumberColumn = true;

	$list->SQL = "
       SELECT * FROM (SELECT grefid,
                             gdsksdesc || ': ' || ". IDEAPartsID::get('goal_statement') ." AS gsentance,
                             NULL AS bsentance,
                             NULL AS orefid,
                             order_num AS g_num,
                             NULL AS b_num,
                             g.iepyear
                        FROM webset.std_oth_goals g
			                 INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON g.area_id = ksa.gdskrefid
			                 LEFT OUTER JOIN webset.disdef_bgb_ksaconditions cond ON g.cond_id = cond.crefid
			                 LEFT OUTER JOIN webset.disdef_bgb_ksaksgoalactions verb ON g.verb_id = verb.gdskgarefid
			                 LEFT OUTER JOIN webset.disdef_bgb_scpksaksgoalcontent cont ON g.content_id = cont.gdskgcrefid
			                 LEFT OUTER JOIN webset.disdef_bgb_measure measur ON g.meas_id = measur.mrefid
			                 LEFT OUTER JOIN webset.disdef_bgb_ksaeval sched ON g.sched_id = sched.erefid
                       WHERE stdrefid = " . $tsRefID . "
                         AND iepyear = " . $siymrefid . "
                         AND esy = 'N'
                       UNION ALL
                      SELECT g.grefid,
                             NULL AS gsentance,
                             o.objective_own AS bsentance,
                             orefid,
                             g.order_num AS g_num,
                             o.order_num AS b_num,
                             g.iepyear
                        FROM webset.std_oth_goals g
			                 INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON g.area_id = ksa.gdskrefid
			                 INNER JOIN webset.std_oth_objectives o ON g.grefid = o.grefid
			                 LEFT OUTER JOIN webset.disdef_bgb_ksaconditions cond ON g.cond_id = cond.crefid
			                 LEFT OUTER JOIN webset.disdef_bgb_ksaksgoalactions verb ON g.verb_id = verb.gdskgarefid
			                 LEFT OUTER JOIN webset.disdef_bgb_scpksaksgoalcontent cont ON g.content_id = cont.gdskgcrefid
			                 LEFT OUTER JOIN webset.disdef_bgb_measure measur ON g.meas_id = measur.mrefid
			                 LEFT OUTER JOIN webset.disdef_bgb_ksaeval sched ON g.sched_id = sched.erefid
                       WHERE stdrefid = " . $tsRefID . "
                         AND iepyear = " . $siymrefid . "
                         AND esy = 'N'
                     ) as t
        ORDER BY COALESCE(g_num, 0), COALESCE(grefid, 0), COALESCE(b_num, 0), COALESCE(orefid, 0)";

	$list->addColumn('Goal/Objective')
		->dataCallback('markGoalsObjectives')
		->width('60%');

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

	$print_button = FFPrintButton::factory(CoreUtils::getURL('goals_with_grades_print.ajax.php', array('dskey' => $dskey, 'print_dskey' => $print_ds->getKey(), 'siymrefid' => $siymrefid)));

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
		$col = $col - 1;
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
						->toHTML() . $progress[$i]["sprnarative"];
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
						->toHTML()  . $progress[$i]["sprnarative"];
				}
			}
		}
		return FFMenuButton::factory('Empty')
			->addItem('Add', 'addProgress(' . $periods[$col]['bmnum'] . ', ' . $data['grefid'] . ', ' . (int)$data['orefid'] . ', ' . $periods[$col]['dsyrefid'] . ')')
			->css('font-style', 'italic')
			->css('font-weight', 'normal')
			->toHTML();
	}

?>
<script type="text/javascript">
	function addProgress(period, grefid, orefid, dsyrefid) {
		api.goto(
			'goals_with_grades_add.php',
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

	function editProgress(sprrefid, orefid) {
		api.goto(
			'goals_with_grades_add.php',
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
			'goals_with_grades_delete.php',
			{
				'dskey': $("#dskey").val(),
				'siymrefid': $("#siymrefid").val(),
				'sprrefid': sprrefid
			}
		);
	}
</script>