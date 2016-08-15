<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Sp Ed Enrollment Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
        SELECT tsrefid,
               " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdname') . " AS stdname,
               gl_code,
               stdenterdt,
               dencode || ' - ' || dendesc,
               stdexitdt,
               dexcode || ' - ' || dexdesc,
               stdschid,
               stdstateidnmbr,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment AS ts
               LEFT OUTER JOIN public.sys_usermst AS u ON u.umrefid = ts.umrefid
               " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
               " . IDEAParts::get('enrollJoin') . "
               " . IDEAParts::get('exitJoin') . "
         WHERE std.vndrefid = VNDREFID
         ORDER BY 2,3
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField("Enrollment Date Range", "stdenterdt", "date_range");
	$list->addSearchField(FFSwitchYN::factory('Blank Enrollment Date')
		->sqlField("CASE WHEN stdenterdt IS NOT NULL THEN 'N' ELSE 'Y' END"));
	$list->addSearchField(FFIDEAEnrollCodes::factory())->sqlField('ts.denrefid')->name('denrefid');
	$list->addSearchField("Exit Date Range", "stdexitdt", "date_range");
	$list->addSearchField(FFIDEAExitCodes::factory())->sqlField('ts.dexrefid')->name('dexrefid');
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('School Name', '', 'group')->sqlField('vouname');
	$list->addColumn('Student Name', '20%')->sqlField('stdname');
	$list->addColumn('Grade', '5%');
	$list->addColumn('Date Entered Sp Ed Program', '10%')->type('date');
	$list->addColumn('Sp Ed Enrollment Code', '30%');
	$list->addColumn('Date Exited Sp Ed Program', '10%')->type('date');
	$list->addColumn('Sp Ed Exit Code', '20%');
	$list->addColumn('Student #');
	$list->addColumn('State #');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->getPrinter()->setPageFormat(RCPageFormat::LETTER | RCPageFormat::LANDSCAPE);

	$list->printList();
?>
