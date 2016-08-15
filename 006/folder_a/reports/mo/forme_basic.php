<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Form E: District-Wide Assessments';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus,
		       fem.yesornot,
               fe.assessment,
               fe.accomodation,
               fe.assesswhynot,
               fe.assesswhyalt,
		       ts.tsrefid,
		       fe.syrefid
		  FROM webset.std_form_e_mst AS fem
		       INNER JOIN webset.std_form_e_dtl AS fe ON (fe.syrefid = fem.syrefid AND fe.assmode = CASE WHEN fem.yesornot = 'Y' THEN 'D' ELSE 'A' END)
		       INNER JOIN webset.std_iep_year iep ON (fem.syrefid = iep.siymrefid AND siymcurrentiepyearsw = 'Y')
		       INNER JOIN webset.sys_teacherstudentassignment AS ts ON (iep.stdrefid = ts.tsrefid)
		       " . IDEAParts::get('studentJoin') . "
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY stdlnm, stdfnm 
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory())->sqlField('ts.umrefid');
	$list->addSearchField(FFIDEAGradeLevel::factory())->sqlField('std.gl_refid');
	$list->addSearchField(FFSwitchYN::factory('Will participate'))->sqlField('fem.yesornot');
	$list->addSearchField('Assessment')->sqlField('fe.assessment')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Alternate Assessment/Accommodations')->sqlField('fe.accomodation')->sqlMatchType(FormFieldMatch::SUBSTRING);


	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Will participate')->sqlField('yesornot')->type('switch');
	$list->addColumn('Assessment')->sqlField('assessment');
	$list->addColumn('Alternate Assessment/Accommodations')->sqlField('accomodation');
	$list->addColumn('Why cannot participate')->sqlField('assesswhynot');
	$list->addColumn('Appropriate alternate')->sqlField('assesswhyalt');


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
