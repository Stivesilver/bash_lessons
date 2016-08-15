<?php
    Security::init();

    $list = new listClass();
    $list->title = 'Federal ID #';
    $list->showSearchFields = true;
    $list->printable = true;

    $list->SQL = "
        SELECT stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
               vouname,
               gl_code,
               stdfedidnmbr
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
        ->data(array(1=>'Blank Federal ID #', 2=>'None Blank Federal ID #'))
        ->sqlField("CASE WHEN COALESCE(stdfedidnmbr,'') = '' THEN 1 ELSE 2 END");

    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('Grade');
    $list->addColumn('Federal ID #');

    $list->printList();

?>