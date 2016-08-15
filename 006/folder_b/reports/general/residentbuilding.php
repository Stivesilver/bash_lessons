<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Resident Building Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->printCallback('printTable')
	;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT UPPER(SUBSTR(std.stdlnm, 1, 1)) || '...' AS firstletter,
			   " . IDEAParts::get('stdname') . " AS stdname,
               gl_code,
               " . IDEAParts::get('disability') . " AS mdisability,
               webset.vou_res(std.stdrefid) AS resschname,
               " . IDEAParts::get('username') . " AS cmfullname,
               " . IDEAParts::get('stddob') . " AS stddob,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment  AS ts
               LEFT OUTER JOIN public.sys_usermst AS cm ON cm.umrefid = ts.umrefid
               " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
         WHERE std.vndrefid = VNDREFID
         ADD_SEARCH
		 ORDER BY resschname, stdname, std.stddob
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Resident Building', '', 'group')
		->sqlField('resschname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Date of Birthl')->sqlField('stddob')->width(20);
	$list->addColumn('Disability')->sqlField('mdisability');
	$list->addColumn('Case Manager')->sqlField('cmfullname');
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
