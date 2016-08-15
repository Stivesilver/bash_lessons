<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Sp Ed Students';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->printCallback('printTable')
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
		       " . IDEAParts::get('stdname') . " AS stdname,
		       gl_code,
		       " . IDEAParts::get('stdsex') . " AS stdsex,
		       " . IDEAParts::get('stddob') . " as stddob,
		       " . IDEAParts::get('stdenterdt') . " as stdenterdt,
		       " . IDEAParts::get('stdenrolldt') . " as initsped,
		       " . IDEAParts::get('disability') . " AS mdisability,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.sys_teacherstudentassignment ts
		       " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
		       " . IDEAParts::get('schoolJoin') . "
		 WHERE std.vndrefid = VNDREFID
		 ADD_SEARCH
		 ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')->sqlField('vouname');

	$list->addColumn('Student')->sqlField('stdname')->width(20);
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('School')->sqlField('vouname')->width(20);
	$list->addColumn('Gender')->sqlField('stdsex');
	$list->addColumn('DOB')->sqlField('stddob');
	$list->addColumn('Date Entered Sp Ed')->sqlField('stdenterdt');
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
