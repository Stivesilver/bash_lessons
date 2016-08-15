<?php
    Security::init();

    $list = new listClass();
    $list->title = 'Statewide And District Wide Testing';
    $list->showSearchFields = true;
	$list->printable = true;

    $grades = db::execSQL("
    	SELECT gl_refid, gl_code
	      FROM c_manager.def_grade_levels
	     WHERE vndrefid = VNDREFID
	     ORDER BY gl_numeric_value
	")->keyedCol(0,1);

	$testedStudents = db::execSQL("
		SELECT iep.stdrefid
	      FROM webset.std_iep_year iep
	           INNER JOIN webset.std_registry reg ON iep_year = siymrefid    
	     WHERE siymcurrentiepyearsw = 'Y'
	       AND srkeygroup = 'oh_iep' 
	       AND srkeyname = 'assessment_part'
	       AND srkeydata = 'Y'	
	")->indexCol(0);
	
    $list->SQL = "
        SELECT std.stdrefid,
               " . IDEAParts::get('stdname') . " AS stdname,
	           vouname,	           
	           ohsdtdesc,
	           gl_code,	           
   	           ohssdtstategrdlevelrefid,
               passedogt,
               ohssdtstatewilltaketestwaccommsw,
               ohssdtstatewillparticipatealternateassesssw,
	           accommodations
	      FROM webset.sys_teacherstudentassignment ts
          	   " . IDEAParts::get('studentJoin') . "	           
	           " . IDEAParts::get('repSchoolJoin') . "
	           " . IDEAParts::get('gradeJoin') . "
	           INNER JOIN webset.std_oh_sw_dw_test test ON ts.tsrefid = test.stdrefid
	           INNER JOIN webset.statedef_oh_sw_dw_test state ON test.arearefid = state.ohsdtrefid
	           INNER JOIN webset.std_iep_year ON iepschoolyearrefid = siymrefid 
	     WHERE std.vndrefid = VNDREFID
	       AND tsrefid in (" . implode(',', $testedStudents). ")
	       AND siymcurrentiepyearsw = 'Y'
	     ORDER BY 2
    ";

    $list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory(true));
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFSwitchAI::factory('Sp Ed Status'))
                ->value('A')
                ->sqlField("CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END")
                ->name('spedstatus');
    
    $list->addSearchField('Subject', 'ohsdtrefid', 'list')
		->sql("
            SELECT ohsdtrefid, ohsdtdesc
	          FROM webset.statedef_oh_sw_dw_test
	         WHERE (enddate IS NULL or now()< enddate)
	           AND screfid = " . VNDState::factory()->id . "
             ORDER BY ohsdtdisplayseq
        ");
        
    $list->addSearchField(FFGradeLevel::factory())
    	->sqlField('std.gl_refid')
    	->caption('Actual Grade');
    	
    $list->addSearchField('Testing Grade', 'ohsdtrefid', 'list')
		->sql("
            SELECT gl_refid, gl_code
	          FROM c_manager.def_grade_levels
	         WHERE vndrefid = VNDREFID
	         ORDER BY gl_numeric_value
        ")
        ->sqlField("
        	',' || ohssdtstategrdlevelrefid::varchar || ',' like '%,' || ADD_VALUE || ',%'
        ");
    
    $list->addSearchField(FFSwitchYN::factory('Passed OGT'), 'passedogt');
    $list->addSearchField(FFSwitchYN::factory('With Accommodations'), 'ohssdtstatewilltaketestwaccommsw');
    $list->addSearchField(FFSwitchYN::factory('Modified Assessment'), 'ohssdtstatewillparticipatealternateassesssw');


    $list->addColumn('Student Name');
    $list->addColumn('Reporting School');
    $list->addColumn('State-wide Assessment');
    $list->addColumn('Actual Grade');
    $list->addColumn('Testing Grade')->dataCallback('showGrades');
    $list->addColumn('Passed OGT')->type('switch');
    $list->addColumn('With Accommodations')->type('switch');
    $list->addColumn('Modified Assessment')->type('switch');
    $list->addColumn('Detail Of Accommodations');

    $list->printList();
    
    function showGrades($data, $col) { 
        global $grades;        
        $arrayCode = array();
        
        foreach (explode(',', $data[$col]) as $grade_id) {
        	if ($grade_id > 0) $arrayCode[] = $grades[$grade_id];        	 
		}
		
		return implode(', ', $arrayCode);
    }

?>