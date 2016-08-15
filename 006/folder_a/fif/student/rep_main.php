<?php
	Security::init();
	FIFParts::init();

    $list = new ListClass();

    $list->title = "504 Processes Report";
    $list->showSearchFields = true;
    $list->printable = true;

    $list->SQL = "
		SELECT hisrefid,
			   " . IDEAParts::get('stdname') . ",
			   vouname,
			   gl_code,
			   difdesc,              
			   initdate,
               exitdate,
               CASE WHEN " . FIFParts::get('fifActivePlain') . " THEN 'Y' ELSE 'N' END as fifstatus
		  FROM webset.vw_dmg_studentmst AS std
		       INNER JOIN webset.std_fif_history fif ON std.stdrefid = fif.stdrefid
		       INNER JOIN webset.disdef_fif_status status ON fif.difrefid = status.difrefid
               INNER JOIN webset.def_fif_status state ON status.statecode_id = state.fifrefid
		  	   " . IDEAParts::get('gradeJoin') . "
		  	   " . IDEAParts::get('repSchoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
		 ORDER BY 2,3
	";

    $list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFGradeLevel::factory('std.gl_refid'));
	$list->addSearchField(FFIDEASchool::factory(true), "")->name('vourefid');
	$list->addSearchField(FFSwitchYN::factory("Active Student"), "CASE WHEN COALESCE(std.stdstatus, 'A') = 'A' THEN 'Y' ELSE 'N' END ")->value('Y');
	$list->addSearchField(FFSwitchYN::factory("504 Active"), "CASE WHEN " . FIFParts::get('fifActivePlain') . " THEN 'Y' ELSE 'N' END")->value('Y');
	
	$list->addSearchField('District 504 Process', "fif.difrefid", 'list')		
		->sql("
			SELECT difrefid,
	               difdesc
	          FROM webset.disdef_fif_status district
	               INNER JOIN webset.def_fif_status state ON state.fifrefid = district.statecode_id
	         WHERE vndrefid = VNDREFID
	           AND (state.enddate IS NULL OR NOW() < state.enddate)
	           AND (district.enddate IS NULL OR NOW() < district.enddate)
	         ORDER BY difdesc
		");
	
	 $list->addSearchField('Form', "
            EXISTS (SELECT 1
                      FROM webset.std_fif_forms forms                           
                     WHERE forms.hisrefid = fif.hisrefid 
                       AND frefid = ADD_VALUE)
        ", 'list')
        ->sql("
			SELECT frefid,
                   cname || ' / ' || fname
              FROM webset.disdef_fif_forms f
                   LEFT OUTER JOIN webset.disdef_fif_form_category c ON f.fcrefid = c.fcrefid
             WHERE f.vndrefid = VNDREFID
                   AND (f.enddate IS NULL OR NOW() < f.enddate)
             ORDER BY cname, f.seqnum, fname
		");

	$list->addColumn("Student")->type("repeat");	
	$list->addColumn("School");
	$list->addColumn('Grade Level');
	$list->addColumn('504 Process');	
	$list->addColumn('504 Initial Referral Date')->sqlField('initdate')->type('date');
	$list->addColumn('Forms')->sqlField('hisrefid')->dataCallback('completedForms');
	$list->addColumn('504 Exit Date')->sqlField('exitdate')->type('date');
	$list->addColumn('504 Active')->hint('504 Status')->type('switch')->sqlField('fifstatus');
	
    $list->printList();    
    
    function completedforms($data, $col) {        
        $arr = db::execSQL("
        		SELECT fname
	              FROM webset.std_fif_forms s
	                   INNER JOIN webset.disdef_fif_forms f ON f.frefid = s.frefid              
	             WHERE hisrefid = " . $data['hisrefid'] . "
	             ORDER BY s.lastupdate DESC
        	")->indexCol(0);
        return count($arr) . (count($arr) == 1 ? ' form ' : ' forms ') . (count($arr) > 0 ? '(' . implode(', ', $arr) . ')' : '') ;
    }   

?>