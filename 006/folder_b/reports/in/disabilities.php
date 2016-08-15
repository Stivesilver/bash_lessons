<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Disability Report by Disabilities';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdname') . " AS stdname,
               disdef.dccode || ' - ' || disdef.dcdesc AS stddis,
               public.plpgsql_recs_to_str('SELECT CAST(COALESCE(pd.silclrecode, '''') AS VARCHAR) AS column
                                             FROM webset.std_in_lre_selections AS pc
                                                  INNER JOIN webset.statedef_in_lre_codes AS pd ON pd.silcrefid = pc.silcrefid
                                                  INNER JOIN webset.statedef_in_lre_selection_codes AS sc ON sc.silsclreselectioncode = pc.silsclreselectioncode AND sc.silscrejectioncodesw != ''Y''
                                            WHERE pc.stdrefid = '||ts.tsrefid||'
                                              AND pd.silcearlychildhoodsw  = '''||COALESCE(ts.stdearlychildhoodsw,'N')||'''
                                            ORDER BY 1', ', ') AS plcode,
               plpgsql_recs_to_str('SELECT rsd.strcode AS column, CAST(rs.bcpseqnumber AS VARCHAR) AS order_column
                                      FROM webset.std_srv_rel AS rs
                                           INNER JOIN webset.statedef_services_rel AS rsd ON rsd.strrefid = rs.stsrefid
                                     WHERE rs.stdrefid = '||ts.tsrefid||'
                                     ORDER BY order_column', ', ') AS relsrv,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment ts
               INNER JOIN webset.std_disabilitymst AS dis ON dis.stdrefid = ts.tsrefid
               INNER JOIN webset.statedef_disablingcondition AS disdef ON disdef.dcrefid = dis.dcrefid
               " . IDEAParts::get('studentJoin') . "
	           " . IDEAParts::get('gradeJoin') . "
	           " . IDEAParts::get('schoolJoin') . "
         WHERE std.vndrefid = VNDREFID
         ADD_SEARCH
         ORDER BY stddis, vouname, stdname, gl_code, plcode
    ";

	$list->addSearchField(FFIDEADisability::factory())->sqlField('dis.dcrefid');
	$list->addSearchField(FFSelect::factory('Disability Type')
			->sql("
				SELECT CAST(0 AS VARCHAR) AS validvalueid, ' All' AS dccode
	             UNION ALL
	            SELECT validvalueid, validvalue
	   	    	  FROM webset.glb_validvalues
				 WHERE valuename = 'DisabilityType'
	             ORDER BY validvalueid
		")
			->sqlField('dis.sdtype')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')
		->sqlField('stddis');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Placement')->sqlField('plcode');
	$list->addColumn('Related Services')->sqlField('relsrv');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_casemanagermst')
			->setKeyField('umrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
