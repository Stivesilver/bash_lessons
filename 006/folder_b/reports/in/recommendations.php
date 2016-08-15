<?php
	Security::init();

	$list = new listClass();
	$list->title = 'Testing Questions';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
        SELECT std.stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,
	           gl_code,
	           dsydesc,
               t1.ditaitext,
               t0.sitainarrtext,
               narr2options,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_teacherstudentassignment ts
          	   " . IDEAParts::get('studentJoin') . "	           
	           " . IDEAParts::get('repSchoolJoin') . "
	           " . IDEAParts::get('gradeJoin') . "
	           INNER JOIN webset.std_in_test_assessment_info AS t0 ON ts.tsrefid = t0.stdrefid
               INNER JOIN webset.disdef_in_test_assessment_info AS t1 ON t1.ditairefid = t0.ditairefid
               INNER JOIN webset.std_common t2 ON in_test_quest = t0.dsyrefid AND ts.tsrefid = t2.stdrefid
               INNER JOIN webset.disdef_schoolyear t3 ON in_test_quest = t3.dsyrefid
	     WHERE std.vndrefid = VNDREFID
	     ORDER BY 2
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory(true));
	$list->addSearchField(FFIDEASchoolYear::factory()->sqlField('t3.dsyrefid'));
	$list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
	$list->addSearchField(FFSwitchAI::factory('Sp Ed Status'))
		->value('A')
		->sqlField("CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END")
		->name('spedstatus');

	$list->addSearchField(FFGradeLevel::factory())
		->sqlField('std.gl_refid')
		->caption('Grade');

	$list->addSearchField('Question', 't1.ditairefid', 'select')
		->sql("
			SELECT ditairefid, ditaitext
			  FROM webset.disdef_in_test_assessment_info
			 WHERE vndrefid = VNDREFID
			 ORDER BY ditairefid
		");

	$list->addColumn('Student')->width('10%');
	$list->addColumn('School')->width('10%');
	$list->addColumn('Grade')->width('4%');
	$list->addColumn('School Year')->width('6%');
	$list->addColumn('Question')->width('25%');
	$list->addColumn('Assessments')->width('25%')->dataCallback('listToTable');
	$list->addColumn('Accommodations')->width('20%')->dataCallback('listToTable');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->printList();

	function listToTable($data, $col) {
		return implode(', <br><br>', explode(',', $data[$col]));
	}

?>