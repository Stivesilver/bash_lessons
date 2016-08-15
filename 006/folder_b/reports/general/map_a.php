<?php

	Security::init();

	$list = new listClass();
	$list->title = 'MAP-A Students Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
       SELECT " . IDEAParts::get('schoolName') . " AS vouname,
	          " . IDEAParts::get('stdname') . " AS stdname,
	          " . IDEAParts::get('disability') . " AS mdisability,
	          gl_code,
	          0 AS cmcounter,
	          " . IDEAParts::get('username') . " AS casemanager,
	          " . IDEAParts::get('placecode') . " AS plcode,
	          CASE WHEN fd.young_map_a = 'Y' THEN 'Grades 3-8' ELSE '' END || ' ' ||
              CASE WHEN fd.naep_mapa = 'Y' THEN 'NAEP' ELSE '' END || ' ' ||
              CASE WHEN fd.act_mapa = 'Y' THEN 'ACT' ELSE '' END || ' ' ||
              CASE WHEN fd.eligible = 'Y' THEN 'Grades 9-12' ELSE '' END AS mapa,
              CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
			  CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	     FROM webset.sys_teacherstudentassignment ts
	          " . IDEAParts::get('studentJoin') . "
	          " . IDEAParts::get('gradeJoin') . "
	          " . IDEAParts::get('schoolJoin') . "
	          " . IDEAParts::get('casemanJoin') . "
              LEFT JOIN webset.std_form_d AS fd ON (fd.stdrefid = ts.tsrefid)
              INNER JOIN webset.std_iep_year AS iep ON (fd.syrefid = iep.siymrefid AND iep.siymcurrentiepyearsw = 'Y')
	    WHERE std.vndrefid = VNDREFID
          AND (young_map_a = 'Y' OR naep_mapa = 'Y' OR act_mapa = 'Y' OR eligible = 'Y')
	   ORDER BY gl_numeric_value, stdname, mdisability
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')
		->sqlField('gl_code');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Disability')->sqlField('mdisability');
	$list->addColumn('Placement Code')->sqlField('plcode');
	$list->addColumn('Building')->sqlField('vouname');
	$list->addColumn('Case Manager')->sqlField('casemanager');
	$list->addColumn('MAP-A')->sqlField('mapa');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('stdrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
