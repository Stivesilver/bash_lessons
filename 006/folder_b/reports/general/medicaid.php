<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Medicaid';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
	           stdfnm,
	           substr(stdmnm,0,1) as stdmnm,
	           stdlnm,
	           gl_code,
	           " . IDEAParts::get('stddob') . " AS stddob,
	           stdfedidnmbr,
	           stdmedicatenum,
	           stdstateidnmbr,
	           CASE stdsex WHEN 1 THEN 'M' WHEN 2 THEN 'F' END AS stdsex,
	           " . IDEAParts::get('stdiepmeetingdt') . " as stdiepmeetingdt,
	           " . IDEAParts::get('stdenrolldt') . " as stdenrolldt,
	           " . IDEAParts::get('stdcmpltdt') . " as stdcmpltdt,
	           " . IDEAParts::get('stdevaldt') . " as stdevaldt,
	           " . IDEAParts::get('stdtriennialdt') . " as stdtriennialdt,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.vw_dmg_studentmst std
	           LEFT OUTER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.stdrefid
	         " . IDEAParts::get('schoolJoin') . "
             " . IDEAParts::get('gradeJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ADD_SEARCH
	     ORDER BY 1, upper(stdLNM), upper(stdFNM)
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('gl_code'));
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')
		->sqlField('vouname');
	$list->addColumn('First Name')->sqlField('stdfnm');
	$list->addColumn('Middle')->sqlField('stdmnm');
	$list->addColumn('Last Name')->sqlField('stdlnm');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('DOB')->sqlField('stddob');
	$list->addColumn('Gnd')->sqlField('stdsex');
	$list->addColumn('Federal ID #')->sqlField('stdfedidnmbr');
	$list->addColumn('State ID #')->sqlField('stdstateidnmbr');
	$list->addColumn('Medicaid #')->sqlField('stdmedicatenum');
	$list->addColumn('IEP Meeting')->sqlField('stdiepmeetingdt');
	$list->addColumn('IEP Initiation')->sqlField('stdenrolldt');
	$list->addColumn('Annual Review')->sqlField('stdevaldt');
	$list->addColumn('Triennial')->sqlField('stdtriennialdt');
	$list->addColumn('Gnd')->sqlField('stdsex');
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
