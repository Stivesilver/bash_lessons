<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Summary/Recommendations';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
        SELECT tsrefid,
               " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdname') . " AS stdname,
			   TO_CHAR(siymiepbegdate, 'MM/DD/YYYY') || ' - ' || TO_CHAR(siymiependdate, 'MM/DD/YYYY') as iepyear,
               stddob,
               gl_code,
               siaitext,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment AS ts
               " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
			   INNER JOIN webset.std_iep_year AS iep ON ts.tsrefid = iep.stdrefid
               INNER JOIN webset.std_additionalinfo ai ON iep.siymrefid = ai.iepyear AND COALESCE(ai.docarea, 'A') = 'A'
         WHERE std.vndrefid = VNDREFID
         ORDER BY 2,3,siymiepbegdate DESC
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField('Student Folder', '', 'select')
		->sql("
			SELECT TO_CHAR(dsybgdt, 'YYYY') || ' - ' || TO_CHAR(dsyendt, 'YYYY'),
				   dsydesc
			  FROM webset.disdef_schoolyear
			 WHERE vndrefid = VNDREFID
			 ORDER BY dsybgdt DESC
		")
		->sqlField("TO_CHAR(siymiepbegdate, 'YYYY') || ' - ' || TO_CHAR(siymiependdate, 'YYYY')")
		->name('folder');

	$list->addSearchField('Summary/Recommendation', 'siaitext')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());
	$list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

	$list->addColumn('School Name', '', 'group')->sqlField('vouname');
	$list->addColumn('Student Name', '10%')->sqlField('stdname');
	$list->addColumn('Student Folder', '10%')->sqlField('iepyear');
	$list->addColumn('Grade', '10%')->sqlField('gl_code');
	$list->addColumn('DOB', '10%')->sqlField('stddob')->type('date');
	$list->addColumn('Summary/Recommendation', '50%')->sqlField('siaitext');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->getPrinter()->setPageFormat(RCPageFormat::LETTER | RCPageFormat::LANDSCAPE);

	$list->printList();
?>
