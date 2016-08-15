<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Gifted';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
		       gl_code,
		       student504,
		       " . IDEAParts::get('stdsex') . " AS stdsex,
		       to_char(stddob, 'mm/dd/yyyy') as stddob,
		       " . IDEAParts::get('schoolName') . " AS vouname,
		       giftedprogram,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.sys_teacherstudentassignment ts
		       " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
		       " . IDEAParts::get('schoolJoin') . "
		 WHERE std.vndrefid = VNDREFID
		   ADD_SEARCH
		 ORDER BY vouname, 	gl_numeric_value, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField('Gifted Program', 'giftedprogram', 'list')
		->sql("
            SELECT sc_code, sc_code || ' - ' || sc_short_name
              FROM c_manager_statedef.sc_gifted
			 WHERE sc_statecode = '" . VNDState::factory()->code . "'
		 	 ORDER BY 2
        ");
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')
		->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Gender')->sqlField('stdsex');
	$list->addColumn('Date of Birth')->sqlField('stddob');
	$list->addColumn('Gifted Program')->sqlField('giftedprogram');
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
