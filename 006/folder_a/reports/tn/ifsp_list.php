<?php

	Security::init();

	$list = new listClass();
	$list->title = 'IFSP Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->printCallback('printTable')
	;

	$list->SQL = "
				SELECT ts.tsrefid,
				       ts.stdrefid,
				       stdlnm,
				       stdfnm,
				       stdmnm,
				       " . IDEAParts::get('schoolName') . " || ' ' || COALESCE(' - ' || " . IDEAParts::get('username') . ", '') AS school,
				       gl_code,
				       " . IDEAParts::get('stdiepmeetingdt') . " AS stdiepmeetingdt,
				       CASE
				       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
				       ELSE 'N'
				       END AS stdstatus,
				       CASE
				       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
				       ELSE 'N'
				       END AS spedstatus,
				       stdcmpltdt AS stdcmpltdt_real,
				       stdtriennialdt AS stdtriennialdt_real,
				       stdlnm || ', ' || stdfnm
				  FROM webset.sys_teacherstudentassignment ts " . IDEAParts::get('studentJoin') . " " . IDEAParts::get('gradeJoin') . " " . IDEAParts::get('casemanJoin') . " " . IDEAParts::get('schoolJoin') . " " . IDEAParts::get('enrollJoin') . "
				 WHERE std.vndrefid = VNDREFID ADD_SEARCH
				 ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory())->caption('IFSP Status');

	$list->addColumn('Lumen ID');
	$list->addColumn('Last Name');
	$list->addColumn('First Name');
	$list->addColumn('Middle Name');
	$list->addColumn('Attending School');
	$list->addColumn('Grade');
	$list->addColumn('Meeting Date');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('IFSP')->hint('IFSP Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
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
