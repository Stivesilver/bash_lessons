<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Related Services Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE);

	$list->SQL = "
         SELECT " . IDEAParts::get('schoolName') . " AS vouname,
                " . IDEAParts::get('stdname') . " AS stdname,
                gl_code,
                " . IDEAParts::get('stdsex') . " AS stdsex,
                " . IDEAParts::get('stddob') . " as stddob,
                ethcode,
                CASE WHEN service   	like 'Other%'  THEN COALESCE('<i>' || serv_other   || '</i>', '') ELSE service   		END as service,
                CASE WHEN frequency 	like 'Other%'  THEN COALESCE('<i>' || freq_other   || '</i>', '') ELSE frequency 		END as frequency,
                CASE WHEN dur.duration like 'Other%'  THEN COALESCE('<i>' || duration_oth || '</i>', '') ELSE dur.duration   END as duration,
                CASE WHEN location     like 'Other%'  THEN COALESCE('<i>' || loc_other    || '</i>', '') ELSE location       END as location,
                " . IDEAParts::get('disability') . " as stddis,
                siymiepbegdate,
                siymiependdate,
			    CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
                CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
           FROM webset.sys_teacherstudentassignment ts
                INNER JOIN webset_tx.std_srv_related AS srv ON ts.tsrefid = srv.stdrefid
                INNER JOIN webset_tx.def_srv_frequency freq ON freq.frefid = srv.freq
                INNER JOIN webset_tx.def_srv_duration dur ON dur.drefid = srv.duration
                INNER JOIN webset_tx.def_srv_locations loc ON loc.lrefid = srv.loc
                INNER JOIN webset.std_iep_year AS year ON srv.iep_year = year.siymrefid
                INNER JOIN webset_tx.def_srv_related rel ON rrefid = srefid
                " . IDEAParts::get('studentJoin') . "
                " . IDEAParts::get('gradeJoin') . "
                " . IDEAParts::get('schoolJoin') . "
                LEFT OUTER JOIN webset.statedef_ethniccode AS eth ON eth.ethrefid = std.stdeth
          WHERE std.vndrefid = VNDREFID
          ORDER BY vouname, stdname,  srv.refid
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField("Student Folder Range", "", "date_range")
		->name('start_date')
		->nameTo('end_date')
		->valueTo(date('Y-m-d'))
		->value((date('Y')-1) . date('-m-d'))
		->sqlField("(siymiepbegdate BETWEEN COALESCE(NULLIF(ADD_VALUE1, '')::DATE, '1000-01-01'::DATE) AND COALESCE(NULLIF(ADD_VALUE2, '')::DATE, '1000-01-01'::DATE) OR siymiependdate BETWEEN COALESCE(NULLIF(ADD_VALUE1, '')::DATE, '1000-01-01'::DATE) AND COALESCE(NULLIF(ADD_VALUE2, '')::DATE, '1000-01-01'::DATE))");
	$list->addSearchField(FFMultiSelect::factory('Related Services'))
		->sql("
			SELECT rrefid,
			       service
			  FROM webset_tx.def_srv_related
			 ORDER BY seqnum
		")
		->selectAll(true)
		->sqlField('rel.rrefid');
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('School Name', '', 'group')->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->setColumnsGroup('Student Folder');
	$list->addColumn('Start Date')->sqlField('siymiepbegdate')->type('date');
	$list->addColumn('End Date')->sqlField('siymiependdate')->type('date');
	$list->setColumnsGroup();
	$list->addColumn('School')->sqlField('vouname');
	$list->addColumn('DOB')->sqlField('stddob')->type('date');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Related Services')->sqlField('service');
	$list->addColumn('Frequency')->sqlField('frequency');
	$list->addColumn('Duration')->sqlField('duration');
	$list->addColumn('Location')->sqlField('location');
	$list->addColumn('Handicap')->sqlField('stddis');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->getPrinter()->setPageFormat(RCPageFormat::LETTER | RCPageFormat::LANDSCAPE);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('stdrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
