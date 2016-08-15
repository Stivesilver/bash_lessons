<?php
    Security::init();
	IDEAListParts::init();
	
    $list = new listClass();
    $list->title = 'SPECIAL EDUCATION NEW, CONTINUING AND TRANSFER STUDENT PLACEMENT FORM';
    $list->showSearchFields = true;
	$list->printable = true;

    $list->SQL = "
        SELECT t1.ccdrefid,
               'Case Manager: ' || " . IDEAParts::get('username') . ",
               stdstateidnmbr,
               stdenterdt,               
               stdlnm,
               stdfnm,               
               stddob,
               CASE t3.stdsex||'' WHEN '1' THEN 'M' ELSE 'F' END AS gender,
               ethcode,
               ell_recserv as lep_status, 
               t8.adesc as lep_language,               
               t4.gl_code,           
               ". IDEAListParts::get('plc_field') .",
               serv_hours,
	           serv_mins,
	           ". IDEAListParts::get('dis_field') .",
	           substring(services from '(.+),$'),
	           NULL as infantref,
	           NULL as infantiep,
	           stdiepmeetingdt,
	           stdevaldt	           
          FROM webset.disrep_ccreportdtl AS t1
               LEFT OUTER JOIN webset.sys_teacherstudentassignment AS t2 ON t2.tsrefid = t1.stdrefid
               LEFT OUTER JOIN webset.dmg_studentmst AS t3 ON t3.stdrefid = t2.stdrefid
               LEFT OUTER JOIN c_manager.def_grade_levels AS t4 ON t4.gl_refid = t3.gl_refid               
               LEFT OUTER JOIN webset.statedef_ethniccode AS t7 ON t7.ethrefid = t1.ethniccode
               LEFT OUTER JOIN webset.statedef_prim_lang AS t8 ON t3.ell_primlang = t8.refid                              
               LEFT OUTER JOIN sys_usermst um ON t2.umrefid   = um.umrefid
         WHERE t1.ccrefid = " . io::geti('RefID') . "
               ADD_SEARCH
         ORDER BY 2, UPPER(stdlnm), UPPER(stdfnm)
    ";

    $list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory(true))->name('vourefid')->sqlField('t3.vourefid');
	$list->addSearchField(FFIDEACaseManager::factory())->name('umrefid')->sqlField('t2.umrefid');
    $list->addSearchField(FFSwitchAI::factory('Student Status'), "COALESCE(stdstatus, 'A')")->name('stdstatus')->value('A');
    $list->addSearchField(FFGradeLevel::factory())->sqlField('t3.gl_refid');
    $list->addSearchField('Sp Ed Enter Date', 'stdenterdt', 'date_range');
    $list->addSearchField('Date of Current IEP', 'stdiepmeetingdt', 'date_range');
    $list->addSearchField('Date of Current stdevaldt', 'stdevaldt', 'date_range');

    $list->addColumn("Case Manager")->type('group');
    $list->addColumn("State Student #");
    $list->addColumn("Sp Ed Enter Date")->type('date')->hint('Date New Student entered SPED Program or Transferred in district.');    
    $list->addColumn("Student Last Name");
    $list->addColumn("Student Fisrt Name");    
    $list->addColumn("Birth Date")->type('date');    
    $list->addColumn("Gender");
    $list->addColumn("Ethnicity");
    $list->addColumn("LEP Status")->type('switch');
    $list->addColumn("LEP Language");
    $list->addColumn("Grade");  
    $list->addColumn("Educational Environment");  
    $list->addColumn("Service Hours per Week");  
    $list->addColumn("Service Minutes per Week"); 
    $list->addColumn("Exceptionality");   
    $list->addColumn("Services");
    $list->addColumn("Referred from Infant/Toddler?");
    $list->addColumn("If Yes, IEP date")->type('date');    
    $list->addColumn("Date of Current IEP")->type('date');
    $list->addColumn("Date of Current Eligibility")->type('date');

    $list->printList();

?>