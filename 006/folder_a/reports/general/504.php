<?php

	Security::init();

	$list = new listClass();
	$list->title = '504 Students';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
               gl_code,
               student504,
               " . IDEAParts::get('stdsex') . " AS stdsex,
               to_char(stddob, 'mm/dd/yyyy') as stddob,
               " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdiepmeetingdt') . " as stdiepmeetingdt,
               " . IDEAParts::get('stdenrolldt') . " as stdenrolldt,
               " . IDEAParts::get('stdcmpltdt') . " as stdcmpltdt,
               " . IDEAParts::get('stdevaldt') . " as stdevaldt,
               " . IDEAParts::get('stdtriennialdt') . " as stdtriennialdt,
               " . IDEAParts::get('username') . " as cmname,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment ts
	         " . IDEAParts::get('studentJoin') . "
	         " . IDEAParts::get('casemanJoin') . "
	         " . IDEAParts::get('schoolJoin') . "
             " . IDEAParts::get('gradeJoin') . "
         WHERE std.vndrefid = VNDREFID
           ADD_SEARCH
         ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFSwitchYN::factory('504 Student'), 'student504')->value('Y');
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')
		->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Case Manager')->sqlField('cmname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Date of Birth')->sqlField('stddob');
	$list->addColumn('IEP Meeting')->sqlField('stdiepmeetingdt');
	$list->addColumn('IEP Initiation')->sqlField('stdenrolldt');
	$list->addColumn('Annual Review')->sqlField('stdcmpltdt');
	$list->addColumn('Evaluation Date')->sqlField('stdevaldt');
	$list->addColumn('Triennial')->sqlField('stdtriennialdt');
	$list->addColumn('Gnd')->sqlField('stdsex');
	$list->addColumn('504 Student')->type('switch')->sqlField('student504');
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
