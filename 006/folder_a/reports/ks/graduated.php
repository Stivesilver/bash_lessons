<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Sp Ed Enrollment Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
        SELECT tsrefid,
               stdfnm,
               stdmnm,
               stdlnm,
               stddob,
               stdschid,
               stdstateidnmbr,
               stdexitdt,
               dexcode || ' - ' || dexdesc,
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
         ORDER BY stdlnm, stdfnm
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField("Enrollment Date Range", "stdenterdt", "date_range");
	$list->addSearchField("Exit Date Range", "stdexitdt", "date_range");
	$list->addSearchField(FFIDEAExitCodes::factory())->sqlField('ts.dexrefid')->name('dexrefid')->value(13);
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory())->value('I');

	$list->addColumn('First Name');
	$list->addColumn('Middle Name');
	$list->addColumn('Last Name');
	$list->addColumn('Date of Birth')->type('date');
	$list->addColumn('Local ID#');
	$list->addColumn('KIDS ID # (State #)');
	$list->addColumn('Date Exited Sp Ed Program')->type('date');
	$list->addColumn('Sp Ed Exit Code')->printable(false);
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>