<?php

	Security::init();

	$list = new listClass();
	$list->title = '504 Students';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
	           " . IDEAParts::get('stdname') . " AS stdname,
	           gl_code,
	           aaadesc as subject,
	           swadesc || COALESCE (' ' || sswanarr, '') as assessment,
	           TRIM(plpgsql_recs_to_str ('SELECT COALESCE(categor || '': '', '''') || pmdesc AS column
	                                        FROM webset.disdef_progmod acc
	                                             LEFT OUTER JOIN webset.disdef_progmodcat cat ON cat.catrefid = acc.catrefid
	                                       WHERE refid IN (' || COALESCE(accomm_ids,'') || '0)
	                                             $inside
	                                       ORDER BY cat.seqnum, categor, acc.seqnum, pmdesc
	                                       LIMIT 40', '\r\n')) as accommodation,
	           validvalueid as asmode
	      FROM webset.std_assess_state std_ass
	           INNER JOIN webset.statedef_assess_links  links ON lrefid = swarefid
	           INNER JOIN webset.statedef_assess_state  ass ON ass.swarefid = assessment_id
	           INNER JOIN webset.statedef_assess_acc    sbj ON sbj.aaarefid = subject_id
	           INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.tsrefid = std_ass.stdrefid
	           INNER JOIN webset.std_iep_year AS year ON std_ass.iepyear = year.siymrefid and siymcurrentiepyearsw='Y'
	           INNER JOIN webset.glb_validvalues AS asmode ON std_ass.assessmode = asmode.refid::varchar
	         " . IDEAParts::get('studentJoin') . "
	         " . IDEAParts::get('casemanJoin') . "
	         " . IDEAParts::get('schoolJoin') . "
             " . IDEAParts::get('gradeJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ADD_SEARCH
	     ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField('Grade', '', 'select_check')
		->sql("
			SELECT gl_refid, gl_code
              FROM c_manager.def_grade_levels
             WHERE vndrefid = VNDREFID
             ORDER BY gl_numeric_value
		");
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')
		->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Case Manager')->sqlField('cmname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Date of Birth')->sqlField('stddob');
	$list->addColumn('IEP Meeting')->sqlField('stdiepmeetingdt');
	$list->addColumn('IEP Initiation')->sqlField('stdenrolldt');
	$list->addColumn('Annual Review')->sqlField('stdcmpltdt');
	$list->addColumn('Evaluation Date')->sqlField('stdevaldt');
	$list->addColumn('Triennial')->sqlField('stdtriennialdt');
	$list->addColumn('Gnd')->sqlField('stdsex');
	$list->addColumn('504 Student')->type('switch')->sqlField('student504');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('stdrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
