<?php
	Security::init();

	$tmp_dmg_studentmst = StdHTempTable::factory()->syd_refid(21)->debug()->toSQLTable();
	
	$list = new ListClass('list1');
	
	$list->title = "Students";
		
	$list->SQL = "
		SELECT s.stdrefid, s.stdlnm, s.stdfnm, gl.gl_code
		  FROM webset.dmg_studentmst AS s
		  	   INNER JOIN webset.dmg_studentmst_history AS sh ON sh.stdrefid = s.stdrefid 
		  	   INNER JOIN " . $tmp_dmg_studentmst . " AS st ON st.stdhrefid = sh.stdhrefid
		  	   INNER JOIN c_manager.def_grade_levels AS gl ON gl.gl_refid = sh.gl_refid
		  	   INNER JOIN c_manager.def_buildings AS cb ON cb.vourefid = sh.vourefid 
		 WHERE s.vndrefid = 1
		   AND s.stdstatus = 'A'
		 ORDER BY LOWER(s.stdlnm), LOWER(s.stdfnm), gl.gl_code
   	";
		
	$list->addColumn("Student Last Name");	
	$list->addColumn("Student First Name");	
	$list->addColumn("Grade Level");	
	
	$list->printList(); 
?>