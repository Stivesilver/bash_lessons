<?PHP

	Security::init();

	$dskey = io::get('dskey');
	$print_dskey = io::get('print_dskey');
	$ds = DataStorage::factory($dskey);
	$print_ds = DataStorage::factory($print_dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = IDEAStudent::factory($tsRefID);
	$siymrefid = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
	$iepyear = IDEAStudentIEPYear::factory($siymrefid);
	$periods = $print_ds->get('periods');
	$goal_objectives = $print_ds->get('goal_objectives');
	$progress = $print_ds->get('progress');

	RCStyle::defineStyleClass('title', '[color: blue; margin-top: 10px; padding: 5px 0px; background: #D1E1FC]');
	RCStyle::defineStyleClass('markperiods', '40% [padding: 10px]');
	RCStyle::defineStyleClass('legend', '40% right [padding: 10px]');

	$style = RCStyle::factory()
		->bgColor('#f5f5f5')
		->border(1, '#aaa')
		->padding(2);

	$table = RCTable::factory('100%')
		->border(1, '#aaa')
		->addRow($style)
		->addColumn('Goals/Objectives', '60%');

	for ($i = 1; $i <= count($periods); $i++) {
		$table->addColumn($periods[$i]['bm'] . ' / ' . $periods[$i]['dsydesc'], 'center bold');
	}

	foreach ($goal_objectives as $data) {
		$table->addRow()
			->cellPadding(4)
			->addCell(markGoalsObjectives($data), $data['gsentance'] ? 'bold' : 'italic');

		for ($i = 1; $i <= count($periods); $i++) {
			$cellData = showProgressMark($data, $progress, $periods, $i);
			if ($cellData !== null) {
				$cellObject = RCLayout::factory()
					->addText((string)$cellData['epsdesc'], 'center bold')
					->newLine()
					->addText((string)$cellData['sprnarative'], 'center italic');
			} else {
				$cellObject = null;
			}
			$table->addCell($cellObject);
		}
	}

	/**
	 * Marking Periods Details
	 */
	$perTable = RCTable::factory()
		->addTitle('Marking Periods', '[font-size: 12px]')
		->border(1, '#aaa')
		->addRow($style)
		->addColumn('Title', '40%')
		->addColumn('Period', '60%');

	foreach ($periods as $period) {
		$perTable->addRow()
			->addCell($period['bm'] . ' / ' . $period['dsydesc'])
			->addCell($period['bmbgdt'] . ' - ' . $period['bmendt'], 'center');
	}

	/**
	 * Legend Block
	 */
	$extents = IDEADistrict::factory(SystemCore::$VndRefID)->getProgressExtents();

	$legTable = RCTable::factory()
		->addTitle('Legend', '[font-size: 12px]')
		->border(1, '#aaa')
		->addRow($style)
		->addColumn('Code', '20%')
		->addColumn('Description', '80%');

	foreach ($extents as $extent) {
		/** @var IDEADistrictProgressExtent $extent */
		$extent;

		$legTable->addRow()
			->addCell($extent->get(IDEADistrictProgressExtent::F_CODE))
			->addCell($extent->get(IDEADistrictProgressExtent::F_DESCRIPTION));
	}

	RCDocument::factory(RCPageFormat::LANDSCAPE)
		->addTitle(SystemCore::$VndName, 'italic')
		->addTitle('IEP Goals Progress Report', $style)
		->newLine()
		->addText('<b>Student\'s Name:</b> ' . $student->get('stdname'), '50% [font-size: 12px]')
		->addText('<b>IEP Year:</b> ' . $iepyear->getIEPYearPeriod(), '50% right [font-size: 12px]')
		->newLine()
		->addObject($table)
		->newLine()
		->addText('')
		->newLine()
		->addText('')
		->addObject($perTable, '.markperiods')
		->addText('', '20%')
		->addObject($legTable, '.legend')
		->open();

	function markGoalsObjectives($data) {
		if ($data['gsentance'] == '') {
			return 'Objective ' . $data['g_num'] . '.' . $data['b_num'] . ' ' . $data['bsentance'];
		} else {
			return 'Goal ' . $data['g_num'] . ' ' . $data['gsentance'];
		}
	}

	function showProgressMark($data, $progress, $periods, $col) {
		if ($data['gsentance'] == '') {
			for ($i = 0; $i < count($progress); $i++) {
				if ($progress[$i]['dsyrefid'] == $periods[$col]['dsyrefid'] &&
					$progress[$i]['sprmarkingprd'] == $periods[$col]['bmnum'] &&
					$progress[$i]['stdgoalrefid'] == $data['grefid'] &&
					$progress[$i]['stdbenchmarkrefid'] == $data['orefid']
				) {
					return $progress[$i];
				}
			}
		} else {
			for ($i = 0; $i < count($progress); $i++) {
				if ($progress[$i]['dsyrefid'] == $periods[$col]['dsyrefid'] &&
					$progress[$i]['sprmarkingprd'] == $periods[$col]['bmnum'] &&
					$progress[$i]['stdgoalrefid'] == $data['grefid'] &&
					$progress[$i]['stdbenchmarkrefid'] == ''
				) {
					return $progress[$i];
				}
			}
		}
		return null;
	}

?>