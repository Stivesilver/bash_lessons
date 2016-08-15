<?php
    Security::init();

    $list = new listClass();
    $list->title = 'Title I';
    $list->showSearchFields = true;
	$list->printable = true;

    $list->SQL = "
        SELECT stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,
	           gl_code,
	           stdtitle_i
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
	
	$list->addSearchField(FFSwitchYN::factory('Title I'), 'stdtitle_i');

    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('Grade');
    $list->addColumn('Title I')->type('switch');

    $list->printList();

?>