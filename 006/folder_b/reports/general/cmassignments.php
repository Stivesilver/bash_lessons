<?php

	Security::init();

	$list = new listClass();
	$list->title = 'CM Assignments';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
      SELECT " . IDEAParts::get('username') . " AS cmfullname,
             " . IDEAParts::get('stdname') . " AS stdname,
             gl_code,
             stdschid,
             " . IDEAParts::get('disability') . " AS mdisability,
             0 AS cmcounter,
             CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
             CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
        FROM webset.sys_casemanagermst AS cm
             INNER JOIN public.sys_usermst AS um ON um.umrefid = cm.umrefid
             INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.umrefid = um.umrefid
             " . IDEAParts::get('studentJoin') . "
             " . IDEAParts::get('gradeJoin') . "
       WHERE std.vndrefid = VNDREFID
       ADD_SEARCH
       ORDER BY 1,2
    ";

	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Case Manager', '', 'group')
		->sqlField('cmfullname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Student #')->sqlField('stdschid');
	$list->addColumn('Disability')->sqlField('mdisability');
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
