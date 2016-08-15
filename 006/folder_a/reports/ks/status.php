<?php

	Security::init();

	$list = new listClass();
	$list->title = '504 Students';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdname') . " AS stdname,
               CAST(statuscode AS VARCHAR)||' - '||shortdesc as statuscode,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment ts
               LEFT OUTER JOIN webset.statedef_status_code AS stcat ON ts.state_status = stcat.statuscode
               " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('schoolJoin') . "
         WHERE std.vndrefid = VNDREFID
         ADD_SEARCH
         ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFSelect::factory('State Status Code')
			->sql("
				SELECT statuscode,
					   CAST(statuscode AS VARCHAR)||' - '||shortdesc,
					   2
				  FROM webset.statedef_status_code
				 WHERE screfid = " . VNDState::factory()->id . "
				 ORDER BY 3, 2
		")
			->sqlField('ts.state_status')
	);
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')
		->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('State Status Code')->sqlField('statuscode');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('stdrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
