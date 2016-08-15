<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Archived IEP';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
			   gl_code,
		       " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('username') . " as username,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus,
		       ts.tsrefid
		  FROM webset.sys_teacherstudentassignment AS ts
		       " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
		       " . IDEAParts::get('schoolJoin') . "
		       " . IDEAParts::get('casemanJoin') . "
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY stdname
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('um.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));

	$list->addSearchField('School Year', '', 'select')
		->emptyOption(false)
		->value(get_years_period(0))
		->data(
			array(
				get_years_period(1)=> get_years_period(1),
				get_years_period(0)=> get_years_period(0),
				get_years_period(-1)=> get_years_period(-1),
				get_years_period(-2)=> get_years_period(-2)
			)
		)
		->sqlField("'<schoolyear>ADD_VALUE</schoolyear>' IS NOT NULL");

	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('School')->sqlField('vouname');
	$list->addColumn('Case Manager')->sqlField('username');
	$list->addColumn('IEP Year')->dataCallback('checked_iepyears');
	$list->addColumn('Archived')->type('switch')->dataCallback('archived_check');
	$list->addColumn('IEP Meeting Date')->type('date')->dataCallback('archived_date');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

	$list->printList();

	function get_years_period($index) {
		return db::execSQL("
			SELECT CASE WHEN now()> (TO_CHAR(now(), 'yyyy') || '-06-01')::timestamp THEN TO_CHAR(now() + interval '" . $index . " year', 'yyyy') ELSE TO_CHAR(now() + interval '" . ($index - 1) . " year', 'yyyy') END || '-' ||
				   CASE WHEN now()> (TO_CHAR(now(), 'yyyy') || '-06-01')::timestamp THEN TO_CHAR(now() + interval '" . ($index + 1) . " year', 'yyyy') ELSE TO_CHAR(now() + interval '" . $index . " year', 'yyyy') END 
			")->getOne();
	}

	function archived_check($data, $col) {
		global $list;
		
		preg_match('/<schoolyear>(.*)<\/schoolyear>/s', $list->getFinalSQL(), $matches);
		$schoolyear = $matches[1];

		$SQL = "
			SELECT 1
			  FROM webset.std_iep iep
			 WHERE iep.stdrefid = " . $data['tsrefid'] . "
			   AND EXISTS (
					SELECT 1
					  FROM webset.std_iep_year iepyear
					 WHERE iep.stdrefid = iepyear.stdrefid
					   AND TO_CHAR(siymiepbegdate, 'yyyy') || '-' || TO_CHAR(siymiependdate, 'yyyy') = '" . $schoolyear . "'
					   AND (
							   iep.iepyear = iepyear.siymrefid
							OR iep.stdenrolldt BETWEEN siymiepbegdate AND siymiependdate
						   )
			       )
			";
		$res = db::execSQL($SQL)->getOne();
		return strlen($res) == 0 ? 'N' : 'Y';
	}

	function archived_date($data, $col) {
		global $list;
		
		preg_match('/<schoolyear>(.*)<\/schoolyear>/s', $list->getFinalSQL(), $matches);
		$schoolyear = $matches[1];

		$SQL = "
			SELECT stdiepmeetingdt
			  FROM webset.std_iep iep
			 WHERE iep.stdrefid = " . $data['tsrefid'] . "
			   AND EXISTS (
					SELECT 1
					  FROM webset.std_iep_year iepyear
					 WHERE iep.stdrefid = iepyear.stdrefid
					   AND TO_CHAR(siymiepbegdate, 'yyyy') || '-' || TO_CHAR(siymiependdate, 'yyyy') = '" . $schoolyear . "'
					   AND (
							   iep.iepyear = iepyear.siymrefid
							OR iep.stdenrolldt BETWEEN siymiepbegdate AND siymiependdate
						   )
			       )
			ORDER BY stdenrolldt DESC
			";
		return db::execSQL($SQL)->getOne();
	}

	function checked_iepyears($data, $col) {
		global $list;
		
		preg_match('/<schoolyear>(.*)<\/schoolyear>/s', $list->getFinalSQL(), $matches);
		$schoolyear = $matches[1];

		$SQL = "
			SELECT TO_CHAR(siymiepbegdate, 'mm/dd/yyyy') || ' - ' || TO_CHAR(siymiependdate, 'mm/dd/yyyy')
			  FROM webset.std_iep_year iepyear
			 WHERE iepyear.stdrefid = " . $data['tsrefid'] . "
			   AND TO_CHAR(siymiepbegdate, 'yyyy') || '-' || TO_CHAR(siymiependdate, 'yyyy') = '" . $schoolyear . "'
			ORDER BY siymiepbegdate DESC
			";
		return implode(',', db::execSQL($SQL)->indexCol());
	}
?>
