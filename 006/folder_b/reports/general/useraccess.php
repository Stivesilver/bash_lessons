<?php

	Security::init();

	$list = new listClass();
	$list->title = 'User Access Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT ua.stdrefid,
	           um.umrefid,
	           " . IDEAParts::get('username') . " AS cmfullname,
	           " . IDEAParts::get('stdname') . " AS stdname,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.std_useraccess AS ua
	           INNER JOIN public.sys_usermst AS um ON ua.miprefid = um.umrefid
	           INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.tsrefid = ua.stdrefid
	           " . IDEAParts::get('studentJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ADD_SEARCH
	     ORDER BY cmfullname, stdname
    ";

	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('User', '', 'group')
		->sqlField('cmfullname');
	$list->addColumn('Student')->sqlField('stdname');
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
