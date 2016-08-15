<?php

	Security::init();

	$list = new listClass();
	$list->title = 'ESY';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " as vouname,
	           " . IDEAParts::get('stdname') . " AS stdname,
	           gl_code,
	           CASE sieqaanswer WHEN 'Y' then 'Yes' ELSE 'No' END as answer,
	           rec_min,
	           rec_day,
	           rec_wek,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_teacherstudentassignment AS ts
	           INNER JOIN webset.std_in_esy_questions_answers t0 ON ts.tsrefid = t0.stdrefid
	           INNER JOIN webset.statedef_in_esy_questions AS t1 ON t1.sieqrefid = t0.sieqrefid
	           LEFT OUTER JOIN webset.std_in_esy_recommend recs ON recs.stdrefid = ts.tsrefid
               " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ADD_SEARCH
	     ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFSelect::factory('ESY Question')
			->sql("
				SELECT sieqrefid,
                       sieqdesc
                  FROM webset.statedef_in_esy_questions
                 ORDER BY sieqseq, sieqdesc
		")
			->sqlField('t1.sieqrefid')
			->name('quest')
	);
	$list->addSearchField(FFSelect::factory('Answer')
			->sql("
				SELECT validvalueid, validvalue
                  FROM webset.glb_validvalues
			     WHERE (valuename = 'YesNo')
                 ORDER BY validvalueid desc
		")
			->sqlField('sieqaanswer')
			->name('answ')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Attending School', '', 'group')->sqlField('vouname');
	$list->addColumn('Student', '')->sqlField('stdname');
	$list->addColumn('Grade', '')->sqlField('gl_code');
	$list->addColumn('Answer', '')->sqlField('answer');
	if(io::exists('quest')) {
		if (io::geti('quest') == 4) {
			$list->addColumn('Minutes', '')->sqlField('rec_min');
			$list->addColumn('Days', '')->sqlField('rec_day');
			$list->addColumn('Weeks', '')->sqlField('rec_wek');
		}
	}

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>
