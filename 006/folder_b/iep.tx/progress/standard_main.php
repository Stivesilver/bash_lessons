<?php
    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
    $esy        = io::get('ESY') == 'Y' ? 'Y' : 'N';

    $iepyear = db::execSQL("
        SELECT *
          FROM webset.std_iep_year
         WHERE siymrefid = " . (int) $siymrefid . "
    ")->assocAll();

    $progress = db::execSQL("
        SELECT sprrefid,
               stdgoalrefid,
               stdbenchmarkrefid,
               sprnarative,
               dsyrefid,
               sprmarkingprd,
               percentofprogress,
               epsdesc
          FROM webset_tx.std_sb_progress std
               INNER JOIN webset.disdef_progressrepext ext ON std.eprefid = ext.eprefid
         WHERE stdrefid = " . $tsRefID . "
         ORDER BY stdgoalrefid, stdbenchmarkrefid, dsyrefid, sprmarkingprd
    ")->assocAll();

    $mperiods = db::execSQL("
        SELECT mrk.*,
               dsydesc
          FROM webset.sch_markperiod mrk
               INNER JOIN webset.vw_dmg_studentmst std ON std.vourefid = mrk.vourefid
               INNER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.stdrefid
               INNER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = mrk.dsyrefid
         WHERE ts.tsrefid = " . $tsRefID . "
         ORDER BY dsy.dsydesc
    ")->assocAll();

    $periods = array();

    $p = 1;
    for ($i = 0; $i < count($mperiods); $i++) {
        $I = 1;
        while ($I < 21) {
            if ($mperiods[$i]["esy" . $I] == $esy and !(($mperiods[$i]["bmbgdt" . $I] < $iepyear[0]["siymiepbegdate"] and
                $mperiods[$i]["bmendt" . $I] < $iepyear[0]["siymiepbegdate"]) or
                ($mperiods[$i]["bmbgdt" . $I] > $iepyear[0]["siymiependdate"] and
                $mperiods[$i]["bmendt" . $I] > $iepyear[0]["siymiependdate"]))) {
                $periods[$p]["bm"] = $mperiods[$i]["bm" . $I];
                $periods[$p]["bmnum"] = $I;
                $bgdt = $mperiods[$i]["bmbgdt" . $I];
                $endt = $mperiods[$i]["bmendt" . $I];
                $periods[$p]["bmbgdt"] = substr($bgdt, 5, 2) . "/" . substr($bgdt, 8, 2) . "/" . substr($bgdt, 0, 4);
                $periods[$p]["bmendt"] = substr($endt, 5, 2) . "/" . substr($endt, 8, 2) . "/" . substr($endt, 0, 4);
                $periods[$p]["dsyrefid"] = $mperiods[$i]["dsyrefid"];
                $periods[$p]["dsydesc"] = $mperiods[$i]["dsydesc"];
                $p++;
            }
            $I++;
            if ($I == 21 or $mperiods[$i]["bm" . $I] == "") {
                break;
            }
        }
    }

	$esyControl = FFCheckBoxList::factory('ESY')
		->name('esy')
		->value($esy)
		->data(array('Y'=>''))
		->displaySelectAllButton(FALSE)
		->onChange("api.goto(api.url('standard_main.php', {'dskey' : '".$dskey."', 'ESY' : $('#esy').val(), 'siymrefid' : $('#siymrefid').val()}))");
    
    $iepyears = FFSelect::factory('IEP Year')
        ->name('siymrefid')
        ->value($siymrefid)
        ->sql("
            SELECT siymrefid,
                   TO_CHAR(siymiepbegdate, 'MM/DD/YYYY') || ' - ' || TO_CHAR(siymiependdate, 'MM/DD/YYYY')
              FROM webset.std_iep_year
             WHERE stdrefid = ".$tsRefID."
             ORDER BY siymiepbegdate DESC
        ")
        ->onChange("api.goto(api.url('standard_main.php', {'dskey' : '".$dskey."', 'ESY' : $('#esy').val(), 'siymrefid' : $('#siymrefid').val()}))");

    $list = new ListClass();
    $list->title = 'Standards Based Goals ' . ($esy == 'Y' ? 'ESY ' : '') . 'Progress Report';
    $list->hideCheckBoxes = true;
	$list->hideNumberColumn = true;

    $list->SQL = "
       SELECT * FROM (SELECT grefid,
                             CASE WHEN subject='Other' THEN othersub ELSE subject END || ': ' || " . IDEAPartsTX::get('goal_statement') . " AS gsentance,
                             NULL AS bsentance,
                             NULL AS orefid,
                             order_num AS g_num,
                             NULL AS b_num,
                             iepyear
                        FROM webset_tx.std_sb_goals g
							 INNER JOIN webset_tx.def_sb_subjects s ON s.subrefid = g.subrefid
							 INNER JOIN webset_tx.def_sb_action ga ON ga.arefid = g.action_id
							 INNER JOIN webset.glb_validvalues gv ON gv.refid = g.timeframe_id
							 INNER JOIN webset_tx.def_sb_criteria gc ON gc.ctrefid = g.criteria_id
                       WHERE stdrefid = " . $tsRefID . "
                         AND iepyear = " . $siymrefid . "
                         AND esy = '" . $esy . "'
                       UNION ALL
                      SELECT g.grefid,
                             NULL AS gsentance,
                             " . IDEAPartsTX::get('objective_statement') . " AS bsentance,
                             orefid,
                             g.order_num AS g_num,
                             o.order_num AS b_num,
                             iepyear
                        FROM webset_tx.std_sb_objectives o
							 INNER JOIN webset_tx.std_sb_goals g ON o.grefid = g.grefid
							 INNER JOIN webset_tx.def_sb_action oa ON oa.arefid = o.action_id
							 INNER JOIN webset.glb_validvalues ov ON ov.refid = o.timeframe_id
							 INNER JOIN webset_tx.def_sb_criteria oc ON oc.ctrefid = o.criteria_id
                       WHERE g.stdrefid = " . $tsRefID . "
                         AND g.iepyear = " . $siymrefid . "
                         AND g.esy = '" . $esy . "'
                     ) as t
        ORDER BY COALESCE(g_num, 0), COALESCE(grefid, 0), COALESCE(b_num, 0), COALESCE(orefid, 0)";

    $list->addColumn('Goal/Objective')
        ->dataCallback('markGoalsObjectives');
    for ($i = 1; $i <= count($periods); $i++) {
        $list->addColumn($periods[$i]['bm'] . ' / ' . $periods[$i]['dsydesc'])
            ->align('center')
            ->dataCallback('showProgressMark');
    }

    $print_button = FFButton::factory('Print')
        ->leftIcon('./img/printer.png')
        //->onClick('api.ajax.process(ProcessType.REPORT, "' . CoreUtils::getURL('standard_print.ajax.php', array('dskey' => $dskey, 'tsRefID' => $tsRefID, 'ESY' => $esy, 'siymrefid' => $siymrefid)) . '")');
	    ->onClick('api.window.open("ProcessType.REPORT", "' . CoreUtils::getURL('standard_print.ajax.php', array('dskey' => $dskey, 'tsRefID' => $tsRefID, 'ESY' => $esy, 'siymrefid' => $siymrefid)) . '")');
		
    $panel = UILayout::factory()
        ->addHTML('', '90%')
        ->addObject($print_button, 'right')
        ->addHTML('', '20px')
        ->addHTML($esyControl->toHTML(), '1px')
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
        return FFButton::factory('', 'addProgress(' . $periods[$col]['bmnum'] . ', ' . $data['grefid'] . ', ' . (int) $data['orefid'] . ', ' . $periods[$col]['dsyrefid'] . ')')
                ->width('50%')
                ->toHTML();
    }
?>
<script type="text/javascript">
    function addProgress(period, grefid, orefid, dsyrefid) {
        api.goto(
            'standard_add.php',
            {
                'dskey': $("#dskey").val(),
                'ESY': $("#esy").val(),
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
            'standard_add.php',
            {
                'dskey': $("#dskey").val(),
                'ESY': $("#esy").val(),
                'siymrefid': $("#siymrefid").val(),
                'sprrefid': sprrefid,
                'orefid': orefid
            }
        );
    }

    function deleteProgress(sprrefid) {
        api.goto(
            'standard_delete.php',
            {
                'dskey': $("#dskey").val(),
                'ESY': $("#esy").val(),
                'siymrefid': $("#siymrefid").val(),
                'sprrefid': sprrefid
            }
        );
    }
</script>