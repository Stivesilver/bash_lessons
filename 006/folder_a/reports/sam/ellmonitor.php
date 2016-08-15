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
	           ell_monitor
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
	
	$list->addSearchField(FFSwitchYN::factory('ELL Monitoring'), 'ell_monitor')->value('Y');

    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('Grade');
    $list->addColumn('ELL Monitoring')->type('switch');

    $list->printList();

?>