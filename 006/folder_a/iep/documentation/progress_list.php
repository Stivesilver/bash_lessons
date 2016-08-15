<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$year = io::geti('year');
	$siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
	$esy = io::get('esy');
	$list = new ListClass();

	$student = new IDEAStudent($tsRefID, $siymrefid);

	$bgbreports = $student->getProgressReportSimpleBGB($esy);

	$ds->set('bgbreports', $bgbreports);
	$progress = '';
	foreach ($bgbreports as $report) {
		if ($report['id'] != '') {
			if ($progress == '') {
				$progress .= $report['id'];
			} else {
				$progress .= ',' . $report['id'];
				$progress .= ',' . $report['id'];
			}
		}
	}
	$arrToList = array();
	$i = 0;

	foreach ($bgbreports as $bgreport) {
		$arrToList[$i]['grefid'] = $bgreport['grefid'];
		$arrToList[$i]['brefid'] = $bgreport['brefid'];
		$arrToList[$i]['goal'] = $bgreport['goal'];
		$arrToList[$i]['objective'] = $bgreport['objective'];
		$arrToList[$i]['spr_refid'] = $bgreport['id'];
		$arrToList[$i]['period_data'] = $bgreport['period_data'];
		if (isset($bgreport['periods'])) {
			$arrToList[$i]['periods'] = json_encode($bgreport['periods']);
		}
		$i++;
	}

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
		->onChange("api.goto(api.url('./progress_list.php', {'dskey' : '" . $dskey . "', 'esy' : '" . $esy . "', 'siymrefid' : this.value}))");

	$list->title = ($esy == 'Y' ? 'ESY ' : '') . 'Progress Report';
	$list->hideCheckBoxes = true;
	$list->pageCount = 2000;
	$list->fillData($arrToList);

	$list->addColumn('Goal/Benchmark')
		->dataCallback('markGoalsObjectives');

	$periods = IDEADistrict::factory(SystemCore::$VndRefID)->getMarkingPeriodsSimple($esy);

	$extentProgressData = db::execSQL("
			SELECT eprefid,
                   epsdesc
              FROM webset.disdef_progressrepext
             WHERE vndrefid = VNDREFID
             ORDER BY epseq, eprefid")->assocAll();

	$extentProgressLabel = array();

	foreach ($extentProgressData as $per) {
		$key = $per['eprefid'];
		$extentProgressLabel[$key] = $per['epsdesc'];
	}

	foreach ($periods as $key => $per) {

		$buttons = UILayout::factory();
		if ($key > 0)
			$buttons->addObject(
				FFButton::factory()
					->leftIcon('prev.png')
					->toolBarView(true, false, true)
					->htmlWrap('')
					->hint('Move Period Data to Left')
					->onClick('moveProgressReport(' . $siymrefid . ', "' . $esy . '", "' . $per['smp_refid'] . '", ' . $periods[$key - 1]['smp_refid'] . ')')
			);
		if ($key < (count($periods) - 1))
			$buttons->addObject(
				FFButton::factory()
					->leftIcon('next.png')
					->toolBarView(true, false, true)
					->htmlWrap('')
					->hint('Move Period Data to Right')
					->onClick('moveProgressReport(' . $siymrefid . ', "' . $esy . '", "' . $per['smp_refid'] . '", ' . $periods[$key + 1]['smp_refid'] . ')')
			);
		$list->addColumn($per['smp_period'])
			->align('center')
			->dataCallback(create_function('$data', 'return periodData($data, ' . $per['smp_refid'] . ');'))
			->append($buttons->toHTML());
	}

	$exportButt =
		FFIDEAExportButton::factory()
			->setTable('webset.std_progress_reporting')
			->setKeyField('spr_refid')
			->setRefids($progress)
			->setDsKey(json_encode($dskey));

	$print_button = FFMenuButton::factory('Print')
		->leftIcon('./img/printer.png')
		->addItem(' PDF (Core Version)', 'buildIEP()', './img/PDF.png');

	$panel = UILayout::factory()
		->addHTML('', '90%')
		->addObject($exportButt, 'right')
		->addObject($print_button, 'right')
		->addHTML('', '20px')
		->addObject($iepyears, 'left');

	$list->addHTML($panel->toHTML(), ListClassElement::CONTROL_PANEL_RIGHT);

	$list->printList();

	function periodData($data, $smp_refid) {
		global $extentProgressLabel;
		$return = null;
		$id = 0;
		$type = '';
		# id refid goal is null then benchmark
		if ($data['brefid'] == '') {
			$id = $data['grefid'];
			$type = 'g';
		} else {
			$id = $data['brefid'];
			$type = 'b';
		}

		$plusBut = FFButton::factory('Add New')
			->rightIcon('plus.png')
			->toolBarView(true, false, true)
			->onClick('addProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
			->name($id . '-fadd-' . $smp_refid . '-' . $type)
			->htmlWrap('')
			->toHTML();

		# not exist data for current goal\benchmark
		if ($data['period_data'] == '') {
			$return = UIElements::factory()
				->addObject(
					FFButton::factory('Delete data')
						->leftIcon('delete.png')
						->name($id . '-del-' . $smp_refid . '-' . $type)
						->toolBarView(true, false, true)
						->hide(true)
						->onClick('delProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
						->htmlWrap('')
				)
				->addObject(
					FFButton::factory('')
						->leftIcon('pencil.png')
						->name($id . '-edit-' . $smp_refid . '-' . $type)
						->toolBarView(true, true, true)
						->hide(true)
						->onClick('addProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
						->htmlWrap('')
				)
				->addObject(
					FFButton::factory('Add New')
						->rightIcon('plus.png')
						->name($id . '-add-' . $smp_refid . '-' . $type)
						->toolBarView(true, false, true)
						->onClick('addProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
						->htmlWrap('')
				)
				->toHTML();
		} else {
			$extentProgress = json_decode($data['period_data'], true);
			# not exist data in db for current period
			if (!isset($extentProgress[$smp_refid])) {
				$return = UIElements::factory()
					->addObject(
						FFButton::factory('Delete data')
							->leftIcon('delete.png')
							->name($id . '-del-' . $smp_refid . '-' . $type)
							->toolBarView(true, false, true)
							->hide(true)
							->onClick('delProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
							->htmlWrap('')
					)
					->addObject(
						FFButton::factory('')
							->leftIcon('pencil.png')
							->name($id . '-edit-' . $smp_refid . '-' . $type)
							->toolBarView(true, true, true)
							->hide(true)
							->onClick('addProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
							->htmlWrap('')
					)
					->addObject(
						FFButton::factory('Add New')
							->rightIcon('plus.png')
							->name($id . '-add-' . $smp_refid . '-' . $type)
							->toolBarView(true, false, true)
							->onClick('addProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
							->htmlWrap('')
					)
					->toHTML();
			} else {
				# data exist add buttons for delete\edit
				$key = $extentProgress[$smp_refid]['extentProgress'];
				$text = $extentProgressLabel[$key];
				$return = UIElements::factory()
					->addObject(
						FFButton::factory('Delete data')
							->leftIcon('delete.png')
							->name($id . '-del-' . $smp_refid . '-' . $type)
							->toolBarView(true, false, true)
							->onClick('delProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
							->htmlWrap('')
					)
					->addObject(
						FFButton::factory($text)
							->leftIcon('pencil.png')
							->name($id . '-edit-' . $smp_refid . '-' . $type)
							->toolBarView(true, true, true)
							->onClick('addProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
							->htmlWrap('')
					)
					->addObject(
						FFButton::factory('Add New')
							->rightIcon('plus.png')
							->name($id . '-add-' . $smp_refid . '-' . $type)
							->toolBarView(true, false, true)
							->hide(true)
							->onClick('addProgressReport(' . $id . ', "' . $type . '", ' . $smp_refid . ')')
							->htmlWrap('')
					)
					->toHTML();
			}
		}

		return $return;
	}

	function markGoalsObjectives($data, $col) {
		global $dskey;
		if ($data['goal'] == '') {
			# benchmark
			$graphs = IDEAStudentBenchmarkAssessment::factory($data['brefid'])->getTrialGraph(null);
			$periodsBech = json_decode($data['periods']);
			$perBechLayout = UILayout::factory();
			foreach ($periodsBech as $key => $value) {
				if ($value->narrative != "") {
					$perBechLayout
						->addHTML('MP-' . $value->bm . ' Comments: ' . $value->narrative)
						->newLine();
				}
			}
			$layout = UILayout::factory()
				->addHTML('', '5%')
				->addHTML($data['objective'])
				->addObject(
					UIAnchor::factory('Documentation')
						->hint('Documentation')
						->css('color', '#0000ff')
						->css('font-weight', 'bold')
						->onClick("callMeasureTests(" . json_encode($data['brefid']) . ", " . json_encode($dskey) . "); api.event.cancel(event);")
				)
				->newLine()
				->addObject($perBechLayout, '[padding-left: 61px; id=' . $data['brefid'] . '] italic')
				->newLine();
			foreach ($graphs as $trial) {
				$layout
					->newLine()
					->addHTML('')
					->newLine()
					->newLine()
					->addHTML($trial['name'], '[padding-left: 87px]')
					->newLine()
					->addHTML($trial['html'], '[padding-left: 87px]')
					->newLine();
			}

			return UICustomHTML::factory($layout)->id('b' . $data['brefid'])->toHTML();
		} else {
			$periodsGl = json_decode($data['periods']);
			$perGolLayout = UILayout::factory();
			foreach ($periodsGl as $key => $value) {
				if ($value->narrative != "") {
					$perGolLayout
						->addHTML('MP-' . $value->bm . ' Comments: ' . $value->narrative)
						->newLine();
				}
			}
			# goal
			$goal = UILayout::factory()
				->addHTML($data['goal'], '[font-weight: bold;]')
				->newLine()
				->addObject($perGolLayout, 'italic');

			return UICustomHTML::factory($goal)->id('g' . $data['grefid'])->toHTML();
		}
	}

	io::jsVar('dskey', $dskey);
	io::jsVar('esy', $esy);
	io::jsVar('siymrefid', $siymrefid);
	io::jsVar('tsRefID', $tsRefID);

?>
<script type="text/javascript">

	function addProgressReport(id, type, smpRefid) {
		var win = api.window.open(
			'Add/Edit Progress',
			api.url(
				'./progress_edit.php',
				{
					'dskey': dskey,
					'esy': esy,
					'siymrefid': siymrefid,
					'id': id,
					'type': type,
					'smpRefid': smpRefid
				}
			)
		);
		win.addEventListener('cm',
			function (e) {
				formCompleted(e.param.refid, e.param.ctype, smpRefid, e.param.period, e.param.narrative);
				changeContent(e.param.refid, e.param.ctype, dskey);
			}
		);
		win.show();
	}

	function formCompleted(id, type, smpRefID, period, narrative) {
		$('#' + id + '-del-' + smpRefID + '-' + type).show();
		$('#' + id + '-edit-' + smpRefID + '-' + type).show();
		$('#' + id + '-edit-' + smpRefID + '-' + type).html('<div style="display: table; border-spacing: 0px; empty-cells: show; width: 100%; -moz-box-sizing: padding-box; min-height:  18px; min-width: 100%"><div style="display: table-cell; width: auto; vertical-align: middle; text-align: center"><img src="../../../../core/interface/lib/img/buttons/pencil.png?ac=446" style="vertical-align: middle;"></div><div style="display: table-cell; width: auto; vertical-align: middle; padding:  0px 4px; white-space:  nowrap; text-align: left">' + period + '</div>');
		$('#' + id + '-add-' + smpRefID + '-' + type).hide();
	}

	function delProgressReport(id, type, smpRefID) {
		if (confirm('Do you really want to delete selected entry?')) {
			api.ajax.post(
				'progress_del.ajax.php',
				{
					'id': id,
					'type': type,
					'smpRefID': smpRefID
				}, function (answer) {
					if (answer.finish == 1) {
						$('#' + id + '-del-' + smpRefID + '-' + type).hide();
						$('#' + id + '-edit-' + smpRefID + '-' + type).hide();
						$('#' + id + '-add-' + smpRefID + '-' + type).show();
						changeContent(id, type, dskey);
					}
				}
			);
		}
	}

	function moveProgressReport(iepyear, esy, per_source, per_dest) {
		api.ajax.post(
			'progress_move.ajax.php',
			{
				'siymrefid': siymrefid,
				'esy': esy,
				'per_source': per_source,
				'per_dest': per_dest
			}, function (answer) {
				if (answer.finish == 1) {
					ListClass.get().reloadPage();
				} else {
					api.alert('System detected that destination Period has some entered data. We afraid to overwrite your data, please make destination Period empty before moving.');
				}
			}
		);
	}

	function buildIEP() {
		api.ajax.process(
			UIProcessBoxType.REPORT,
			api.url('progress_print.ajax.php'),
			{
				'esy': esy,
				'tsRefID': tsRefID,
				'dskey': dskey,
				'siymrefid': siymrefid,
			}
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {

			}
		);
	}

	function callMeasureTests(benchmark_id, dskey) {
		var url = api.virtualRoot + "/apps/idea/iep/bgb/bgb_measure_test_list.php";
		var title = 'Documentation';

		var win = api.desktop.open(
			title,
			api.url(
				url,
				{'benchmark_id': benchmark_id, 'dskey': dskey}
			)
		).maximize().show();
		win.addEventListener(
			ObjectEvent.CLOSE,
			function (e) {
				changeContent(benchmark_id, 'b', dskey);
			}
		)
	}

	function changeContent(id, type, dskey) {
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./gen_progress_content.ajax.php'),
			{
				'refid': id,
				'dskey': dskey,
				'esy': esy,
				'siymrefid': siymrefid,
				'type': type
			},
			true
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				$('#' + type + id).html(e.param.html_cont);
			}
		)

	}

</script>
