<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Services and Frequency';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->printCallback('printTable')
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " as stdname,
	           " . IDEAParts::get('schoolName') . " AS vouname,
	           gl_code,
	           stsdesc,
	           provider,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_teacherstudentassignment ts
	           INNER JOIN webset.std_srv_all ssa ON ssa.stdrefid = ts.tsrefid
	           INNER JOIN webset.statedef_services_type sst ON sst.trefid = ssa.srv_type
	           INNER JOIN webset.statedef_services_all stsa ON stsa.stsrefid = ssa.srvrefid
	           INNER JOIN webset.std_iep_year AS siy ON siy.siymrefid = ssa.iep_year
	           " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
		       " . IDEAParts::get('schoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	       AND typedesc = 'Related Services'
	       AND siymcurrentiepyearsw = 'Y'
	     ADD_SEARCH
	     ORDER BY vouname, stdlnm, stdfnm
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFSelect::factory('Related Service:')
			->sql("
				SELECT 0, 'All', 1
                 UNION ALL
                SELECT stsRefID,
                       COALESCE(stsCode, ' ') || ' - ' || stsDesc,
                       2
                  FROM webset.statedef_services_all
                 WHERE type_id = 2 and screfid=17
                   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                 ORDER BY 1,2
			")
			->sqlField('stsa.stsrefid')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('School', '', 'group')->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Related Servic')->sqlField('stsdesc');
	$list->addColumn('Implementor')->sqlField('provider');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_casemanagermst')
			->setKeyField('umrefid')
			->applyListClassMode()
	);

	$list->printList();

	function printTable(ListClassPrint $lcp) {
		$doc = $lcp->getRCD();
		$data = $lcp->getData();
		$doc->addObject($lcp->getHeading());
		$doc->newLine();
		$doc->addObject($lcp->getDataTable());
		$doc->newLine();
		$doc->addText('Total: ' . count($data), 'right bold');
	}
?>
