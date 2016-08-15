<?php

	Security::init();

	$list = new listClass();
	$list->title = 'CM Assignment Details';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$state = VNDState::factory()->code;

	$list->SQL = "
		 SELECT " . IDEAParts::get('username') . " AS cmfullname,
		        " . IDEAParts::get('stdname') . " AS stdname,
		        gl_code,
		        stdschid,
		        " . IDEAParts::get('disability') . " AS mdisability,
		        " . IDEAParts::get('placement') . " AS plcategory,
		        " . IDEAParts::get('stdcmpltdt') . " AS annualdate,
		        " . IDEAParts::get('stdevaldt') . " AS evaldate,
		        " . IDEAParts::get('stdtriennialdt') . " AS triennialdate,
		        " . IDEAParts::get('stddob') . " as stddob,
		        stdstateidnmbr,
		        CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
		        CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		   FROM webset.sys_casemanagermst AS cm
		        INNER JOIN public.sys_usermst AS um ON um.umrefid = cm.umrefid
		        INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.umrefid = um.umrefid
		         " . IDEAParts::get('studentJoin') . "
		         " . IDEAParts::get('gradeJoin') . "
		  WHERE std.vndrefid = VNDREFID
		  ADD_SEARCH
		  ORDER BY cmfullname, stdtriennialdt, stdname
    ";

	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Case Manager', '', 'group')
		->sqlField('cmfullname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('State ID')->sqlField('stdstateidnmbr');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Disability')->sqlField('mdisability');
	$list->addColumn('Placement Category')->sqlField('plcategory')->width(35);
	$list->addColumn('DOB')->sqlField('stddob');
	$list->addColumn('Annual Date')->sqlField('annualdate');
	$list->addColumn('Eval. date')->sqlField('evaldate');
	$list->addColumn('Trien. Date')->sqlField('triennialdate');
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
