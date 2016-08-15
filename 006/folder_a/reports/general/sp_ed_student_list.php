<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Sp Ed Student List';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$state = VNDState::factory()->code;

	$sql1 = '';
	$sql2 = '';

	if (VNDState::factory()->id == 16) {
		$sql1 = ' edccdeval, edncdeval,';
		$sql2 = 'LEFT OUTER JOIN webset.std_in_eligibility eli ON eli.stdrefid = tsrefid';
	}

	$list->SQL = "
		SELECT tsrefid,
               stdlnm,
               stdfnm,
               stdmnm,
               " . IDEAParts::get('schoolName') . " || ' ' || COALESCE(' - ' || " . IDEAParts::get('stdname') . ", '') AS stdschool,
               gl_code,
               " . IDEAParts::get('spedPeriod') . " AS spedperiod,
               " . IDEAParts::get('stdcmpltdt') . " AS stdcmpltdt,
               " . IDEAParts::get('stdtriennialdt') . " AS stdtriennialdt,
               " . IDEAParts::get('disabcode') . " AS mdisability,
               " . IDEAParts::get('placement') . " AS plcategory,
               " . $sql1 . "
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment ts
	         " . IDEAParts::get('studentJoin') . "
	         " . IDEAParts::get('casemanJoin') . "
	         " . IDEAParts::get('schoolJoin') . "
             " . IDEAParts::get('gradeJoin') . "
             " . IDEAParts::get('enrollJoin') . "
			 " . $sql2 . "
       WHERE std.vndrefid = ".$_SESSION["s_VndRefID"]."
             ADD_SEARCH
       ORDER BY UPPER(stdlnm), UPPER(stdfnm), upper(stdMNM)
    ";

	$a = FFIDEACaseManager::factory('pc')
		->name('umrefid')
		->sqlField('ts.umrefid')
		->emptyOption(false);

	$defCM = db::execSQL($a->sql)->getOne();

	if ($defCM == '') {
		$defCM = '-1';
		$list->SQL = str_replace("ADD_SEARCH", "AND 1=0 ADD_SEARCH", $list->SQL);
	}

	$list->addSearchField($a);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn("Last Name")->sqlField('stdlnm');
	$list->addColumn("First Name")->sqlField('stdfnm');
	$list->addColumn("Middle Name")->sqlField('stdmnm');
	$list->addColumn("Attending School")->sqlField('stdschool');
	$list->addColumn("Grade")->sqlField('gl_code');
	$list->addColumn("Sp Ed Enrollment")->sqlField('spedperiod');
	$list->addColumn("IEP Projected Date&nbsp;<br>&nbsp;of Annual Review")->sqlField('stdcmpltdt');
	$list->addColumn("Projected Triennial Date")->sqlField('stdtriennialdt');
	if (VNDState::factory()->id == 16) {
		$list->addColumn("Date/Current Speech/Language Evaluation")->sqlField('edccdeval');
		$list->addColumn('Date/Next Speech/Language Evaluation')->sqlField('edncdeval');
	} else {
		$list->addColumn("Disability")->sqlField('mdisability');
		$list->addColumn('Placement')->sqlField('plcategory');
	}
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
