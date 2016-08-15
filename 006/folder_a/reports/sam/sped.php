<?php
    Security::init();

    $list = new listClass();
    $list->title = 'Sp Ed Students';
    $list->showSearchFields = true;
	$list->printable = true;

    $list->SQL = "
        SELECT std.stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,
	           gl_code,	           
	           webset.std_sped_status(std.stdrefid, NULL, NULL, current_date) as sped_status
	      FROM webset.vw_dmg_studentmst std
	           " . IDEAParts::get('gradeJoin') . "
	           " . IDEAParts::get('repSchoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ORDER BY 2
    ";

    $list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory(true));
	$list->addSearchField(FFSwitchAI::factory('Sp Ed Status'), "CASE webset.std_sped_status(std.stdrefid, NULL, NULL, current_date) WHEN 'Y' THEN 'A' ELSE 'I' END")->name('sped')->value('A');
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');
	        					   
    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('Grade');    
    $list->addColumn('Sp Ed Student')->type('switch');

    $list->printList();

?>