<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Disability Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
	           " . IDEAParts::get('stdname') . " AS stdname,
	           COALESCE(dccode,'') || ' - ' || dcdesc AS stddis,
	           gl_code,
	           " . IDEAParts::get('username') . " AS cmfullname,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_teacherstudentassignment ts
	           INNER JOIN webset.std_disabilitymst  ON webset.std_disabilitymst.stdrefid = ts.tsrefid
	           INNER JOIN webset.statedef_disablingcondition  ON webset.statedef_disablingcondition.dcrefid = webset.std_disabilitymst.dcrefid
	           LEFT OUTER JOIN public.sys_usermst ON public.sys_usermst.umrefid = ts.umrefid
             " . IDEAParts::get('studentJoin') . "
             " . IDEAParts::get('gradeJoin') . "
	           " . IDEAParts::get('schoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	       AND sdtype = 1
	       ADD_SEARCH
	     ORDER BY vouname, stddis, gl_numeric_value, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')
		->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Disability')->sqlField('stddis');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Case Manager')->sqlField('cmfullname');
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
