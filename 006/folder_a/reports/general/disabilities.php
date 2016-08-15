<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Disability Report by Disabilities';
	$list->showSearchFields = true;
	$list->printable = true;

	if (VNDState::factory()->id == 36) {
		$oh_related = "AND $staterefid != 36
                     UNION ALL
                    SELECT CAST(rsd.strcode AS VARCHAR) AS column, rsd.strcode || '' '' || rsd.strdesc AS order_column
                      FROM webset.std_oh_emis_rel AS rs
                           INNER JOIN webset.statedef_oh_rel_mst AS rsd ON rsd.strrefid = rs.stsrefid
                     WHERE rs.stdrefid = '||ts.tsrefid||'
                       AND $staterefid = 36";
	} else {
		$oh_related = "";
	}

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
		       " . IDEAParts::get('stdname') . " AS stdname,
		       " . IDEAParts::get('disability') . " AS stddis,
		       gl_code,
		       " . IDEAParts::get('placecode') . " AS plcode,
		       plpgsql_recs_to_str('SELECT COALESCE(rsd.strcode, rsd.strdesc) AS column, CAST(rs.bcpseqnumber AS VARCHAR) AS order_column
		                              FROM webset.std_srv_rel AS rs
		                                   INNER JOIN webset.disdef_services_rel AS rsd ON rsd.dtrrefid = rs.dtrrefid
		                             WHERE rs.stdrefid = '||ts.tsrefid||'
		                             UNION
		                            SELECT rsd.strcode AS column, CAST(rs.bcpseqnumber AS VARCHAR) AS order_column
		                              FROM webset.std_srv_rel AS rs
		                                   INNER JOIN webset.statedef_services_rel AS rsd ON rsd.strrefid = rs.stsrefid
		                             WHERE rs.stdrefid = '||ts.tsrefid||'
		                                   $oh_related
		                             ORDER BY order_column, 1', ', ') AS relsrv,
				plpgsql_recs_to_str('SELECT CAST(sdgname AS VARCHAR) AS column
		                               FROM webset.dmg_studentgroupingdtl
		                                    LEFT OUTER JOIN webset.disdef_stddemogrouping ON webset.disdef_stddemogrouping.sdgrefid = webset.dmg_studentgroupingdtl.sdgrefid
		                              WHERE webset.dmg_studentgroupingdtl.stdrefid = '||std.stdrefid||'
		                              ORDER BY sdgname', ', ') AS sdgname,
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

	$list->addSearchField(FFIDEADisability::factory());
	$list->addSearchField(FFStudentName::factory()->sqlField('umrefid'));
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
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
