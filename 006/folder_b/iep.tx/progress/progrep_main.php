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
          FROM webset.std_progressreportmst std
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
		->onChange("api.goto(api.url('progrep_main.php', {'dskey' : '".$dskey."', 'ESY' : $('#esy').val(), 'siymrefid' : $('#siymrefid').val()}))");
		
    
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
        ->onChange("api.goto(api.url('progrep_main.php', {'dskey' : '".$dskey."', 'ESY' : $('#esy').val(), 'siymrefid' : $('#siymrefid').val()}))");

    $list = new ListClass();
    $list->title = ($esy == 'Y' ? 'ESY ' : '') . 'Progress Report';
    $list->hideCheckBoxes = true;
	$list->hideNumberColumn = true;

    $list->SQL = "
       SELECT * FROM (SELECT goal.blrefid,
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
        ORDER BY COALESCE(bl_num, 0), COALESCE(blrefid, 0), COALESCE(g_num, 0), COALESCE(grefid, 0), COALESCE(b_num, 0), COALESCE(brefid, 0)";

    $list->addColumn('Goal/Benchmark')
        ->dataCallback('markGoalsObjectives');
    for ($i = 1; $i <= count($periods); $i++) {
        $list->addColumn($periods[$i]['bm'] . ' / ' . $periods[$i]['dsydesc'])
            ->align('center')
            ->dataCallback('showProgressMark');
    }

    $print_button = FFButton::factory('Print')
        ->leftIcon('./img/printer.png')
        ->onClick('api.ajax.process(ProcessType.REPORT, "' . CoreUtils::getURL('progrep_print.ajax.php', array('dskey' => $dskey, 'tsRefID' => $tsRefID, 'ESY' => $esy, 'siymrefid' => $siymrefid)) . '")');
		
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
                    ->addHTML($data['bl_num'] . '.' . $data['g_num'] . '.' . $data['b_num'] . ' ' . $data['bsentance'], '[color:blue; font-weight: bold;]')
                    ->toHTML();
        } else {
            return UILayout::factory()
                    ->addHTML($data['bl_num'] . '.' . $data['g_num'] . ' ' . $data['gsentance'], '[color:brown; font-weight: bold;]')
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
?>
<script type="text/javascript">
    function addProgress(period, grefid, brefid, dsyrefid) {
        api.goto(
            'progrep_add.php',
            {
                'dskey': $("#dskey").val(),
                'ESY': $("#esy").val(),
                'siymrefid': $("#siymrefid").val(),
                'period': period,
                'grefid': grefid,
                'brefid': brefid,
                'dsyrefid': dsyrefid
            }
        );

    }
    function editProgress(sprrefid, brefid) {
        api.goto(
            'progrep_add.php',
            {
                'dskey': $("#dskey").val(),
                'ESY': $("#esy").val(),
                'siymrefid': $("#siymrefid").val(),
                'sprrefid': sprrefid,
                'brefid': brefid
            }
        );
    }

    function deleteProgress(sprrefid) {
        api.goto(
            'progrep_delete.php',
            {
                'dskey': $("#dskey").val(),
                'ESY': $("#esy").val(),
                'siymrefid': $("#siymrefid").val(),
                'sprrefid': sprrefid
            }
        );
    }
</script>