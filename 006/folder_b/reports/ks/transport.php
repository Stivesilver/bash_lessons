<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Special Transportation';
	$list->showSearchFields = true;
	$list->printable = true;
	$list->getPrinter()->setPageFormat(RCPageFormat::LANDSCAPE);
	$list->getPrinter()->printHeadings(false);

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " as vouname,
	           " . IDEAParts::get('stdname') . " AS stdname,
		       stAide,
		       stHarness,
		       stWheelLift,
		       stTWA,
		       stAirCond,
		       stDoor,
		       doortodoor,
		       taxi,
		       buswithmonitor,
		       stother,
		       stothertxt,
		       stresidenttrans,
		       stneedtrans,
		       safe_restraint,
		       CASE
		       WHEN safe_restraint = 'Y' THEN safe_restraint_oth
		       ELSE NULL
		       END AS safe_restraint_oth,
		       aide2012,
	           " . IDEAParts::get('username') . " AS cmfullname
	      FROM webset.sys_teacherstudentassignment ts
	           INNER JOIN webset.std_transportation ON webset.std_transportation.stdrefid = ts.tsrefid
	          " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
               " . IDEAParts::get('casemanJoin') . "
	     WHERE std.vndrefid = VNDREFID
	       AND stResidentTrans = 'Y'
	       ADD_SEARCH
	     ORDER BY stdname
    ";

	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student', '')->sqlField('stdname');
	$list->addColumn('School')->sqlField('vouname');
	$list->addColumn('Case Manager', '')->sqlField('cmfullname');
	$list->addColumn('Transportation', '')->sqlField('stresidenttrans');
	$list->addColumn('Accommodations', '')->sqlField('stneedtrans');
	$list->addColumn('Wheelchair', '')->sqlField('stwheellift');
	$list->addColumn('Restraint', '')->sqlField('safe_restraint');
	$list->addColumn('Restraint', '')->sqlField('safe_restraint_oth')->width(13);
	$list->addColumn('Curb', '')->sqlField('stdoor');
	$list->addColumn('Door', '')->sqlField('doortodoor');
	$list->addColumn('Aide', '')->sqlField('aide2012');
	$list->addColumn('Specify', '')->sqlField('stothertxt')->width(13);

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>
