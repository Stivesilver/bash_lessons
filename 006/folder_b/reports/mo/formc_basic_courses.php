<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Form C - Course of Study';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
			   'School Year ' || year_num AS school_year,
			   one_coursestudy AS semester_one_course,
			   ARRAY_TO_STRING(
				   ARRAY(
					   SELECT tadesc
						 FROM webset.statedef_transarea
						WHERE ',' || one_area_ids || ',' LIKE '%,' || tarefid::varchar || ',%'
						ORDER BY seqnum
				   ), 
				   ', ' 
			   ) AS semester_one_areas,
			   two_coursestudy AS semester_two_course,
			   ARRAY_TO_STRING(
				   ARRAY(
					   SELECT tadesc
						 FROM webset.statedef_transarea
						WHERE ',' || two_area_ids || ',' LIKE '%,' || tarefid::varchar || ',%'
						ORDER BY seqnum
				   ), 
				   ', ' 
			   ) AS semester_two_areas,
			   notes,
			   CASE
			   WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
			   ELSE 'N'
			   END AS stdstatus,
			   CASE
			   WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
			   ELSE 'N'
			   END AS spedstatus
		  FROM webset.std_form_c_courses AS f
			   INNER JOIN webset.sys_teacherstudentassignment AS ts ON f.stdrefid = ts.tsrefid
			   " . IDEAParts::get('studentJoin') . "
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY stdlnm, stdfnm, year_num, seqnum, refid
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField('School Year', 'f.year_num', 'select')
            ->data(array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6));
	$list->addSearchField('Semester One Courses', 'one_coursestudy')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Semester One Areas', "',' || f.one_area_ids || ',' LIKE '%,ADD_VALUE,%'", 'select')
		->sql("
			SELECT tarefid,
			       tadesc
			  FROM webset.statedef_transarea area
			 WHERE (enddate IS NULL OR now()< enddate)
			 ORDER BY seqnum
		");
	$list->addSearchField('Semester Two Courses', 'two_coursestudy')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Semester Two Areas', "',' || f.two_area_ids || ',' LIKE '%,ADD_VALUE,%'", 'select')
		->sql("
			SELECT tarefid,
			       tadesc
			  FROM webset.statedef_transarea area
			 WHERE (enddate IS NULL OR now()< enddate)
			 ORDER BY seqnum
		");
	$list->addSearchField('Notes', 'notes')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('School Year')->sqlField('school_year');
	$list->addColumn('Semester One Courses')->sqlField('semester_one_course');
	$list->addColumn('Semester One Areas')->sqlField('semester_one_areas');
	$list->addColumn('Semester Two Courses')->sqlField('semester_two_course');
	$list->addColumn('Semester Two Areas')->sqlField('semester_two_areas');
	$list->addColumn('Notes')->sqlField('notes');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>


