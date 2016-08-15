<?php
	
	Security::init(PHP_NOTICE_ON);
	
	//require_once($g_physicalRoot . "/applications/c-manager/includes/performance/ttab.tmp_dmg_studentmst.php");
	//prepare_tmp_dmg_studentmst();
	
	$list = new ListClass('list1');
	
	$list->title = 'Student Demographics';
	
	$list->SQL = "SELECT s.stdRefID,
				s.stdRefID AS lum_id,
				REPLACE(REPLACE(stdLNM, '''', '`'), '\"', '`') AS stdlname,
				REPLACE(REPLACE(stdFNM, '''', '`'), '\"', '`') AS stdfname,
                REPLACE(REPLACE(stdMNM, '''', '`'), '\"', '`'),
                (CASE gl_code is Null WHEN TRUE THEN '<FONT color=red><B>No Data' ELSE gl_code END) AS stdgl,

                'District: ' || vndname || CASE WHEN COALESCE(vndstatecode, '') = '' THEN '' ELSE ' (' || vndstatecode || ')' END || '<br>' ||
                'School: ' || vouname || CASE WHEN COALESCE(voustatecode, '') = '' THEN '' ELSE ' (' || voustatecode || ')' END,
                std_homeroom,
                std_hometeach
           FROM webset.dmg_studentmst AS s
                   INNER JOIN webset.dmg_studentmst_history AS sh ON sh.stdrefid = s.stdrefid
                   INNER JOIN ". StdHTempTable::factory()->toSQLTable() ." AS sht ON sht.stdhrefid = sh.stdhrefid
                   INNER JOIN c_manager.def_buildings AS cb ON cb.vourefid = sh.vourefid
                LEFT OUTER JOIN c_manager.def_grade_levels AS grd ON grd.gl_refid = sh.gl_refid
                                AND grd.vndrefid = " . $_SESSION["s_VndRefID"] . "
                LEFT OUTER JOIN sys_voumst ON sys_voumst.vourefid = sh.vourefid
                LEFT OUTER JOIN sys_vndmst ON sys_vndmst.vndrefid = s.vndrefid
                LEFT OUTER JOIN c_manager.def_websis_schools AS sch ON sch.wsds_refid = sh.res_wsds_refid
          WHERE s.vndrefid = " . $_SESSION["s_VndRefID"] . " ADD_SEARCH
          ORDER BY LOWER(stdLNM), LOWER(stdFNM)";
	
	$list->addSearchField("Last Name", "stdLNM");
	$list->addSearchField("First Name", "stdFNM");
	$list->addSearchField("First Name Goes by",  "stdnickname", "TEXT");
	$list->addSearchField("Guardian Name", "EXISTS ( SELECT 1
	                                                   FROM webset.dmg_guardianmst grd
	                                                  WHERE grd.stdrefid = s.stdrefid
	                                                    AND LOWER(gdlnm||', '||gdfnm) LIKE LOWER(ADD_VALUE||'%'))")
		->append("&nbsp;[Last], [First]");
	$list->addSearchField("Date of Birth",  "stddob", "DATE_RANGE");
	$list->addSearchField("Attending School", "sh.vourefid", "LIST")
		->sql("
		  SELECT vou.vourefid, vou.vouname
			FROM sys_voumst AS vou
		   WHERE vou.vndrefid = " . $_SESSION["s_VndRefID"] . "				 
		   ORDER by LOWER(vou.vouname)
		");
	$list->addSearchField("Resident District", "sch.wsds_state_district_code = TRIM('ADD_VALUE')", "LIST")
		->sql("
		  SELECT DISTINCT wsds_state_district_code, wsds_district_name
			FROM webset.dmg_studentmst_history AS dmg
	  INNER JOIN c_manager.def_websis_schools AS ws  ON dmg.res_wsds_refid = ws.wsds_refid
		   ORDER BY wsds_district_name");
	
	$list->addSearchField("Lumen Student ID #", "s.stdrefid");
	$list->addSearchField("External Student ID #", "s.externalid");
	$list->addSearchField("Federal #", "stdFedIDNmbr");
	$list->addSearchField("State #", "stdstateidnmbr");
	$list->addSearchField("Student Status", "stdStatus", "LIST")
		->value('A')
		->sql("
		SELECT validValueId, validvalue
          FROM webset.glb_validvalues
		 WHERE (valuename = 'UKTStatus')
		");
	$list->addSearchField("Homeroom Teacher", "std_hometeach", "TEXT");
	$list->addSearchField("Grade Level", "sh.gl_refid", "LIST")
		->sql("
		  SELECT gl_refid, gl_code
            FROM c_manager.def_grade_levels
           WHERE vndrefid = " .  $_SESSION["s_VndRefID"] . "
           ORDER BY gl_numeric_value
		");
	$list->addSearchField("Anticipated Graduation School Year", "s.syd_refid_graduation", "LIST")
		->sql("
			SELECT syd_refid, syd_desc
			  FROM c_manager.def_school_years_dis
	   		 WHERE vndrefid = " .  $_SESSION["s_VndRefID"] . "
	   		 ORDER BY syd_begdate");
	$list->addSearchField("Resident Type", "sh.rt_refid", "LIST")
		->sql("
		  SELECT rt_refid, COALESCE(rt_code || ' - ', '') || rt_name
			FROM c_manager.def_resident_type
		   WHERE vndrefid = " .  $_SESSION["s_VndRefID"] . "
		   ORDER BY LOWER(rt_code)");
	$list->addSearchField("School Year", "LENGTH(ADD_VALUE::VARCHAR)>0", "LIST")
		->sql("
		  SELECT syd_refid, syd_desc
			FROM c_manager.def_school_years_dis
		   WHERE vndrefid = " .  $_SESSION["s_VndRefID"] . "
		   ORDER BY syd_begdate
		");
	$list->addSearchField("Mailing Address", "(LOWER(stdhadr1) LIKE LOWER('%'||ADD_VALUE||'%') OR LOWER(stdhcity_m) LIKE LOWER('%'||ADD_VALUE||'%') OR
										LOWER(stdhstate_m) LIKE LOWER('%'||ADD_VALUE||'%') OR LOWER(stdhzip_m) LIKE LOWER('%'||ADD_VALUE||'%') OR
									    EXISTS (SELECT 1
                                               FROM webset.dmg_guardianmst grd
                                               WHERE grd.stdrefid = s.stdrefid
                                                  AND (LOWER(gdadr1) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(gdcity_m) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(gdstate_m) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(gdcitycode_m) LIKE LOWER('%'||ADD_VALUE||'%')
                                                  		))
                                            OR
                                            EXISTS (SELECT 1
                                               FROM webset.dmg_studentmst_addr sa
                                               WHERE sa.stdrefid = s.stdrefid
                                               AND addr_type_sw = 'M'
                                                  AND (LOWER(street_dir) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(street_name) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(street_type) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(house) LIKE LOWER('%'||ADD_VALUE||'%')
                                                  		))
                                            )");
	$list->addSearchField("Residence Address", "(LOWER(stdhadr2) LIKE LOWER('%'||ADD_VALUE||'%') OR LOWER(stdhcity) LIKE LOWER('%'||ADD_VALUE||'%') OR
										LOWER(stdhstate) LIKE LOWER('%'||ADD_VALUE||'%') OR LOWER(stdhzip) LIKE LOWER('%'||ADD_VALUE||'%') OR
									   EXISTS (SELECT 1
                                               FROM webset.dmg_guardianmst grd
                                               WHERE grd.stdrefid = s.stdrefid
                                                  AND (LOWER(gdadr2) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(gdcity) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(gdstate) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(gdcitycode) LIKE LOWER('%'||ADD_VALUE||'%')
                                                  		))
                                            OR
                                            EXISTS (SELECT 1
                                               FROM webset.dmg_studentmst_addr sa
                                               WHERE sa.stdrefid = s.stdrefid
                                               AND addr_type_sw = 'R'
                                                  AND (LOWER(street_dir) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(street_name) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(street_type) LIKE LOWER('%'||ADD_VALUE||'%') OR
                                                  		LOWER(house) LIKE LOWER('%'||ADD_VALUE||'%')
                                                  		))
                                                  		)");
	$list->addSearchField("Phone", "(LOWER(stdphmob) LIKE LOWER('%'||ADD_VALUE||'%') or LOWER(stdhphn) LIKE LOWER('%'||ADD_VALUE||'%') OR
									   EXISTS (SELECT 1
                                               FROM webset.dmg_guardianmst grd
                                               WHERE grd.stdrefid = s.stdrefid
                                                  AND (LOWER(gdhphn) LIKE LOWER('%'||ADD_VALUE||'%') or LOWER(gdwphn) LIKE LOWER('%'||ADD_VALUE||'%') or LOWER(gdmphn) LIKE LOWER('%'||ADD_VALUE||'%'))))")->append("&nbsp;(999) 999-9999");

	$list->multipleEdit = false;
	$list->addColumn("Lumen ID");
	$list->addColumn("Last Name");
	$list->addColumn("First Name");
	$list->addColumn("Middle Name");
	$list->addColumn("Grade");
	$list->addColumn("Attending School");
	$list->addColumn("Homeroom");
	$list->addColumn("Homeroom Teacher");
	
	$list->editURL = "student_demo_edit.php";
    
	$list->printList();
?>
 