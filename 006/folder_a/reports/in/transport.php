<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Special Transportation';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " as vouname,
	           " . IDEAParts::get('stdname') . " AS stdname,
		       gl_code,
		       sistsptransneededexcesstimequestion as f1,
		       sistsptransneededconfcommquestion as f2,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.sys_teacherstudentassignment AS ts
		       INNER JOIN webset.std_in_special_transportation AS t1 ON t1.stdrefid = ts.tsrefid
		       " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
		 WHERE std.vndrefid = VNDREFID
		 ADD_SEARCH
		 ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFSelect::factory('If special transportation is needed, will this transportation result in excess transit time?')
			->sql("
	            SELECT 'N/A', 'N/A', 1 AS order_column
	             UNION
	            SELECT 'Yes', 'Yes', 2 AS order_column
			     UNION
	            SELECT 'No', 'No', 3 AS order_column
	             ORDER BY order_column
		")
			->sqlField('sistsptransneededexcesstimequestion')
	);
	$list->addSearchField(FFSelect::factory('If yes, is this excess transit time needed to meet the needs of the student as determined by the case conference committee?')
			->sql("
		        SELECT 'Yes', 'Yes', 1 AS order_column
				 UNION
				SELECT 'No', 'No', 2 AS order_column
		         ORDER BY order_column
		")
			->sqlField('sistsptransneededconfcommquestion')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Attending School', '', 'group')->sqlField('vouname');
	$list->addColumn('Student', '')->sqlField('stdname');
	$list->addColumn('Grade', '')->sqlField('gl_code');
	$list->addColumn('Transportation result in excess transit time', '')->sqlField('f1');
	$list->addColumn('Is this excess transit time needed tomeet..', '')->sqlField('f2');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>
