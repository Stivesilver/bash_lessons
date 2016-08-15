<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Triennial Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT " . IDEAParts::get('username') . " AS cmfullname,
               " . IDEAParts::get('stdname') . " AS stdname,
               " . IDEAParts::get('disability') . " AS mdisability,
               " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdtriennialdt') . " AS trdate,
		        CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
		        CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_casemanagermst AS cm
               INNER JOIN public.sys_usermst AS um ON cm.umrefid = um.umrefid
               INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.umrefid = um.umrefid
		         " . IDEAParts::get('studentJoin') . "
		         " . IDEAParts::get('schoolJoin') . "
         WHERE std.vndrefid = VNDREFID
         ADD_SEARCH
		 ORDER BY stdtriennialdt
    ";

	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchoolYear::factory());
	$list->addSearchField('Date', 'stdtriennialdt', 'date_range');
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Case Manager', '', 'group')
		->sqlField('cmfullname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Trien. Date')->sqlField('trdate');
	$list->addColumn('Building')->sqlField('vouname');
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
