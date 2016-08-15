<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Purpose of Conference';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
       SELECT " . IDEAParts::get('schoolName') . " AS vouname,
	          " . IDEAParts::get('stdname') . " AS stdname,
	          0 AS cmcounter,
	          " . IDEAParts::get('stdiepmeetingdt') . " AS iepmeetingdate,
	          " . IDEAParts::get('username') . " AS casemanager,
	          " . IDEAParts::get('placecode') . " AS plcode,
			  t1.siepcpdesc || COALESCE(': ' || sicpnarrative, '') AS purp,
              CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
			  CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	     FROM webset.sys_teacherstudentassignment ts
	          " . IDEAParts::get('studentJoin') . "
	          " . IDEAParts::get('gradeJoin') . "
	          " . IDEAParts::get('schoolJoin') . "
	          " . IDEAParts::get('casemanJoin') . "
              LEFT JOIN webset.std_in_iepconfpurpose AS pu ON (pu.stdrefid = ts.tsrefid)
              INNER JOIN webset.statedef_iepconfpurpose AS t1 ON (t1.siepcprefid = pu.siepcprefid)
	    WHERE std.vndrefid = VNDREFID
	   ORDER BY stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFSelect::factory('IEP Purpose of Conference'))
		->sqlField('t1.siepcprefid')
		->sql("
			SELECT siepcprefid, siepcpdesc
			  FROM webset.statedef_iepconfpurpose
			 ORDER BY siep_seq
		");
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Building')->sqlField('vouname');
	$list->addColumn('Case Manager')->sqlField('casemanager');
	$list->addColumn('IEP Purpose of Conference')->sqlField('purp');
	$list->addColumn('IEP Meeting Date')->sqlField('iepmeetingdate');

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
