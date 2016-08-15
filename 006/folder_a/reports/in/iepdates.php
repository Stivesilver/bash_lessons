<?php

	Security::init();

	$list = new listClass();
	$list->title = 'IN IEP Dates';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
      SELECT " . IDEAParts::get('stdname') . " AS stdname,
             gl_code,
             stdschid,
             " . IDEAParts::get('stdcmpltdt') . " AS annualdate,
             " . IDEAParts::get('stdevaldt') . " AS evaldate,
             " . IDEAParts::get('stdtriennialdt') . " AS triennialdate,
             " . IDEAParts::get('stdiepmeetingdt') . " AS stdiepmeetingdt,
             " . IDEAParts::get('stdenrolldt') . " AS stdenrolldt,
             CASE WHEN std.stdrefid IS NULL THEN 0 ELSE 1 END AS stdcounter,
             CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
             CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
        FROM webset.sys_teacherstudentassignment AS ts
             " . IDEAParts::get('studentJoin') . "
             " . IDEAParts::get('gradeJoin') . "
       WHERE std.vndrefid = VNDREFID
       ADD_SEARCH
       ORDER BY 1
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Student #')->sqlField('stdschid');
	$list->addColumn('IEP Meeting')->sqlField('stdiepmeetingdt');
	$list->addColumn('Initiation')->sqlField('stdenrolldt');
	$list->addColumn('Annual')->sqlField('annualdate');
	$list->addColumn('Evaluation')->sqlField('evaldate');
	$list->addColumn('Triennial')->sqlField('triennialdate');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_casemanagermst')
			->setKeyField('umrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
