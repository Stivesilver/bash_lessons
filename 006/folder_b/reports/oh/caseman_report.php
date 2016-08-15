<?php

	Security::init();

	$list = new listClass();
	$list->title = 'CM Assignments';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
     SELECT " . IDEAParts::get('username') . " AS cmfullname,
            " . IDEAParts::get('stdname') . " AS stdname,
		    gl_code,
		    stdschid,
            " . IDEAParts::get('disability') . " AS mdisability,
            " . IDEAParts::get('placecode') . " AS placementcode,
            " . IDEAParts::get('schoolName') . " AS vouname,
            TO_CHAR(stdcmpltdt, 'MM/DD/YYYY') AS annualdate,
            TO_CHAR(stdevaldt, 'MM/DD/YYYY') AS evaldate,
            TO_CHAR(stdtriennialdt, 'MM/DD/YYYY') AS triennialdate,
            plpgsql_recs_to_str('SELECT CAST(sdgname AS VARCHAR) AS column
                                   FROM webset.dmg_studentgroupingdtl
                                        LEFT OUTER JOIN webset.disdef_stddemogrouping ON webset.disdef_stddemogrouping.sdgrefid = webset.dmg_studentgroupingdtl.sdgrefid
                                  WHERE webset.dmg_studentgroupingdtl.stdrefid = ' || std.stdrefid || '
                                  ORDER BY sdgname', ', ') AS sdgname,
            0 AS cmcounter,
            (SELECT stcode
               FROM webset.std_oh_emis_mst
                    INNER JOIN webset.statedef_oh_lre_mst ON webset.statedef_oh_lre_mst.mrefid = webset.std_oh_emis_mst.osemltrefid
              WHERE stdrefid = tsrefid limit 1) as transition
       FROM webset.sys_teacherstudentassignment ts
             " . IDEAParts::get('studentJoin') . "
             " . IDEAParts::get('gradeJoin') . "
             " . IDEAParts::get('schoolJoin') . "
             " . IDEAParts::get('casemanJoin') . "
      WHERE std.vndrefid = VNDREFID
      ORDER BY cmfullname, stdname
    ";

	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFStudentName::factory())->sqlField('cmfullname');
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Case Manager', '', 'group')
		->sqlField('cmfullname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Student #')->sqlField('stdschid');
	$list->addColumn('Disability')->sqlField('mdisability');
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
