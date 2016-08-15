<?php
    Security::init();

    $list = new listClass();
    $list->title = 'ELL Monitoring';
    $list->showSearchFields = true;
	$list->printable = true;

    $list->SQL = "
        SELECT stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,
	           gl_code,
	           federal_lands,
               std_neglected_sw,
               std_privatschool_sw,
               std_schooled_sw,
   		       std_foreign_exchange_sw
	      FROM webset.vw_dmg_studentmst std
	           " . IDEAParts::get('gradeJoin') . "
	           " . IDEAParts::get('repSchoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ORDER BY 2
    ";

    $list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory(true));
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

	$list->addSearchField(FFSwitchYN::factory('Federal Lands'), 'federal_lands');
	$list->addSearchField(FFSwitchYN::factory('Neglected or Delinquent'), 'std_neglected_sw');
	$list->addSearchField(FFSwitchYN::factory('Private School Student'), 'std_privatschool_sw');
	$list->addSearchField(FFSwitchYN::factory('Home Schooled'), 'std_schooled_sw');
	$list->addSearchField(FFSwitchYN::factory('Foreign Exchange Student'), 'std_foreign_exchange_sw');

    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('Grade');
    $list->addColumn('Federal Lands')->type('switch');
    $list->addColumn('Neglected or Delinquent')->type('switch');
    $list->addColumn('Private School Student')->type('switch');
    $list->addColumn('Home Schooled')->type('switch');
    $list->addColumn('Foreign Exchange Student')->type('switch');

    $list->printList();

?>