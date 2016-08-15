<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Disability Report by Disabilities with Federal ID';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
               " . IDEAParts::get('stddob') . " AS stddob,
               gl_code,
               disdef.dccode || ' - ' || disdef.dcdesc AS stddis,
               stdfedidnmbr,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment ts
               INNER JOIN webset.std_disabilitymst AS dis ON dis.stdrefid = ts.tsrefid
               INNER JOIN webset.statedef_disablingcondition AS disdef ON disdef.dcrefid = dis.dcrefid
               " . IDEAParts::get('studentJoin') . "
             " . IDEAParts::get('gradeJoin') . "
         WHERE std.vndrefid = VNDREFID
         ADD_SEARCH
         ORDER BY stdname
    ";

	$list->addSearchField(FFIDEADisability::factory());
	$list->addSearchField(FFSelect::factory('Resident District')
			->sql("
				SELECT CAST(0 AS VARCHAR) AS validvalueid, 'All' AS dccode
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
	$list->addColumn('DOB')->sqlField('stddob');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Federal ID')->sqlField('stdfedidnmbr');
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
