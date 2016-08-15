<?php
	
	Security::init(PHP_NOTICE_ON);
	
	$list = new ListClass('list1');
	
	$list->title = 'Enrollment History';
	
	$list->SQL = "SELECT stdhrefid,

	                         '<font style=\"\"><b>District:</b> ' || ws_rep.wsds_district_name  || CASE WHEN COALESCE(vndstatecode, '') = '' THEN '' ELSE ' (' || vndstatecode || ')' END || '<br/>\n' ||
	                         '<b>School:</b> ' || ws_rep.wsds_school_name  || CASE WHEN COALESCE(voustatecode, '') = '' THEN '' ELSE ' (' || voustatecode || ')' END || '</font>',

	                         '<font style=\"\"><b>District:</b> ' || ws_att.wsds_district_name  || CASE WHEN COALESCE(vndstatecode, '') = '' THEN '' ELSE ' (' || vndstatecode || ')' END || '<br/>\n' ||
	                         '<b>School:</b> ' || ws_att.wsds_school_name  || CASE WHEN COALESCE(voustatecode, '') = '' THEN '' ELSE ' (' || voustatecode || ')' END || '</font>',

	                         gl.gl_code,
	                         '<b>Enrl:</b> '||COALESCE(TO_CHAR(stddtenrol, 'MM/DD/YYYY'),'-')|| '<br/>\n' ||
	                         '<b>Wthdr:</b> '||COALESCE(TO_CHAR(std_withdr_date, 'MM/DD/YYYY'),'-'),
	                         COALESCE(seccode,'')||' - '||COALESCE(secdesc,'')||'/'||	                         
	                         COALESCE(wcode,'') || ' - ' || COALESCE(wdesc,''),
	                         '<b>Activation:</b> '||COALESCE(TO_CHAR(stdh_activation_date, 'MM/DD/YYYY'),'-')|| '<br/>\n' ||
	                         '<b>Deactivation:</b> '||COALESCE(TO_CHAR(stdh_deactivation_date, 'MM/DD/YYYY'),'-'),
	                         CASE WHEN
	                         	(COALESCE(stdh_activation_date, to_date('1000-01-01', 'YYYY-MM-DD'))<=(SELECT syd_enddate FROM c_manager.def_school_years_dis WHERE syd_currentyear='Y' AND vndrefid=" . $_SESSION["s_VndRefID"] . ") AND 	
	                         	COALESCE(stdh_deactivation_date, to_date('3000-01-01', 'YYYY-MM-DD'))>(SELECT syd_begdate FROM c_manager.def_school_years_dis WHERE syd_currentyear='Y' AND vndrefid=" . $_SESSION["s_VndRefID"] . "))
	                         	/*OR (stdh_deactivation_date>(SELECT syd_begdate FROM c_manager.def_school_years_dis WHERE syd_currentyear='Y'))*/
	                         	THEN '#71CE51' ELSE '' END
	                         
	                    FROM webset.dmg_studentmst_history AS h
	                         LEFT OUTER JOIN sys_voumst AS vou ON vou.vourefid = h.vourefid
	                         LEFT OUTER JOIN sys_vndmst AS vnd ON vnd.vndrefid = h.vndrefid
	                         LEFT OUTER JOIN c_manager.def_withdraw AS w ON w.wrefid = h.std_withdr_cod
	                         LEFT OUTER JOIN c_manager.def_buildings AS b ON b.vourefid = h.vourefid
	                         LEFT OUTER JOIN c_manager.def_websis_schools AS ws_rep ON b.wsds_refid = ws_rep.wsds_refid
	                         LEFT OUTER JOIN c_manager.def_websis_schools AS ws_att ON h.att_wsds_refid = ws_att.wsds_refid
	                         LEFT OUTER JOIN c_manager_statedef.mo_sc_school AS mosc_rep ON mosc_rep.sc_refid = ws_rep.wsds_state_record_refid
	                         LEFT OUTER JOIN c_manager_statedef.mo_sc_school AS mosc_att ON mosc_att.sc_refid = ws_att.wsds_state_record_refid
	                         LEFT OUTER JOIN c_manager.def_grade_levels AS gl ON gl.gl_refid = h.gl_refid
	                         LEFT OUTER JOIN c_manager.def_enrollcode AS enr ON enr.secrefid = h.stdenrol_refid
	                   WHERE stdrefid = " . $_REQUEST["stdRefID"] . "
	                   ORDER BY COALESCE(stdh_deactivation_date,TO_DATE('3000-01-01','YYYY-MM-DD')) DESC";
	
	$list->addColumn("Reporting School");    
	$list->addColumn("Attending School");    
	$list->addColumn("GL");    
	$list->addColumn("Enrl Date");    
	$list->addColumn("Enrl/Wthdr");    
	$list->addColumn("Record Activity");
	
	$list->printList();
?>
