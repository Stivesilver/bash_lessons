<?php
    Security::init();

    $list = new listClass();
    $list->title = 'ELL Title III';
    $list->showSearchFields = true;
	$list->printable = true;

    $list->SQL = "
        SELECT stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,
	           gl_code,
	           ell_title3,
	           sc_name
	      FROM webset.vw_dmg_studentmst std
               LEFT OUTER JOIN c_manager_statedef.sc_title_iii sci ON std.ell_title3 = sci.sc_code AND sci.sc_statecode = '" . VNDState::factory()->code . "'
	           " . IDEAParts::get('gradeJoin') . "
	           " . IDEAParts::get('repSchoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ORDER BY 2
    ";

    $list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory(true));
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

	$list->addSearchField('ELL Title III', 'ell_title3', 'list')
		->sql("
            SELECT sc_code, sc_code || ' - ' || sc_name
              FROM c_manager_statedef.sc_title_iii
			 WHERE sc_statecode = '" . VNDState::factory()->code . "'
		 	 ORDER BY 2
        ");


    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('Grade');
    $list->addColumn('Code');
    $list->addColumn('ELL Title III');

    $list->printList();

?>