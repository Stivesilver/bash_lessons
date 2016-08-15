<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Disability Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
	           gl_code,
	           " . IDEAParts::get('stddob') . " AS stddob,
	           stdschid,
	           stdfedidnmbr,
	           " . IDEAParts::get('schoolName') . " AS vouname
	      FROM webset.vw_dmg_studentmst AS std
             " . IDEAParts::get('gradeJoin') . "
	         " . IDEAParts::get('schoolJoin') . "
		WHERE std.vndrefid = VNDREFID
	     ORDER BY vouname, std.gl_refid, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());

	$list->addColumn('', '', 'group')
		->sqlField('vouname');
	$list->addColumn('Grade', '', 'group')
		->sqlField('gl_code');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Date of Birth')->sqlField('stddob');
	$list->addColumn('Student ID #')->sqlField('stdschid');
	$list->addColumn('Federal ID #')->sqlField('stdfedidnmbr');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('stdrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
