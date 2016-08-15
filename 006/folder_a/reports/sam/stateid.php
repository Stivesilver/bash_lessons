<?php
    Security::init();

    $list = new listClass();
    $list->title = 'State ID #';
    $list->showSearchFields = true;
	$list->printable = true;

    $list->SQL = "
        SELECT stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,
	           gl_code,
	           stdstateidnmbr
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
    
    $list->addSearchField('Mode', 'blankornot', 'list')
        ->data(array(1=>'Blank State ID #', 2=>'None Blank State ID #'))
        ->sqlField("CASE WHEN COALESCE(stdstateidnmbr,'') = '' THEN 1 ELSE 2 END");

    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('Grade');
    $list->addColumn('State ID #');

    $list->printList();

?>