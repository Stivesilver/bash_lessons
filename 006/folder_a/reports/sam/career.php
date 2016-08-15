<?php
    Security::init();

    $list = new listClass();
    $list->title = 'Career Education and Cluster';
    $list->showSearchFields = true;
	$list->printable = true;

    $list->SQL = "
        SELECT std.stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,
	           gl_code,	           
	           hce_code,
	           sc_name
	      FROM webset.vw_dmg_studentmst std
	           INNER JOIN (SELECT cte_cluster, stdrefid FROM webset.dmg_studentmst dmg) as dmg ON std.stdrefid = dmg.stdrefid   
               LEFT OUTER JOIN c_manager.def_hs_career_ed sci ON std.stdcareer = sci.hce_refid
               LEFT OUTER JOIN c_manager_statedef.mo_sc_cte_cluster sc ON dmg.cte_cluster = sc.sc_code
	           " . IDEAParts::get('gradeJoin') . "
	           " . IDEAParts::get('repSchoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ORDER BY 2
    ";

    $list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory(true));
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');
	
	$list->addSearchField('Career Education', 'stdcareer', 'list')
		->sql("
            SELECT hce_refid, hce_code 
              FROM c_manager.def_hs_career_ed
			 WHERE (hce_active_sw = 'Y' OR hce_active_sw IS NULL)
			   AND vndrefid = VNDREFID			   
		 	 ORDER BY hce_rank
        ");

	$list->addSearchField('Career Cluster', 'cte_cluster', 'list')
		->sql("
            SELECT sc_code, sc_name 
              FROM c_manager_statedef.mo_sc_cte_cluster			   
		 	 ORDER BY sc_rank
        ");        					  
        					   
    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('Grade');    
    $list->addColumn('Career Education');
    $list->addColumn('Career Cluser');

    $list->printList();

?>