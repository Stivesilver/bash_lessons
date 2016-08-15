<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Special Considerations';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
		       " . IDEAParts::get('stdname') . " AS stdname,
		       gl_code,
		       q.scmsdesc,
		       a.scanswer,
		       sscmnarrative,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus
		  FROM webset.std_spconsid AS sp
		       INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.tsrefid = sp.stdrefid " . IDEAParts::get('studentJoin') . "
		       INNER JOIN webset.statedef_spconsid_quest AS q ON sp.scqrefid = q.scmrefid
		       INNER JOIN webset.statedef_spconsid_answ AS a ON sp.scarefid = a.scarefid
			   INNER JOIN webset.std_iep_year AS year ON sp.syrefid = year.siymrefid AND siymcurrentiepyearsw='Y' 
			   " . IDEAParts::get('gradeJoin') . " 
			   " . IDEAParts::get('schoolJoin') . " 
			   " . IDEAParts::get('casemanJoin') . "
		 WHERE std.vndrefid = VNDREFID
		 ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFSelect::factory('Special Consideration'))
		->sql("
			SELECT scarefid,
			       scmsdesc || ' -> Answer: ' || SUBSTRING(scanswer, 0, 50)
			  FROM webset.statedef_spconsid_answ answ
			       INNER JOIN webset.statedef_spconsid_quest quest ON quest.scmrefid = answ.scmrefid
			 WHERE quest.screfid = " . VNDState::factory()->id . "
			   AND (answ.recdeactivationdt IS NULL or now()< answ.recdeactivationdt)
			   AND (quest.recdeactivationdt IS NULL or now()< quest.recdeactivationdt)
			 ORDER BY seqnum, 2
		")
		->sqlField('sp.scarefid');

	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Question')->sqlField('scmsdesc');
	$list->addColumn('Answer')->sqlField('scanswer');
	$list->addColumn('Narrative')->sqlField('sscmnarrative');
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
