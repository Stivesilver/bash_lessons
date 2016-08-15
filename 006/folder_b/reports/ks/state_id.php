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
               stdlnm,
               stdfnm,
               gl_code,
               stdenterdt,
               dencode || ' - ' || dendesc,
               stdexitdt,
               dexcode || ' - ' || dexdesc,
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

	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('State ID #')->sqlField('stdstateidnmbr');
	$list->addColumn('Last Name')->sqlField('stdlnm');
	$list->addColumn('First Name')->sqlField('stdfnm');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->getPrinter()->setPageFormat(RCPageFormat::LETTER | RCPageFormat::LANDSCAPE);

	$list->printList();
?>
