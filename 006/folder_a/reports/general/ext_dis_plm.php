<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Ext2 number,  Disability, Placement';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
	           " . IDEAParts::get('stdname') . " AS stdname,
	           stdschid,
		       " . IDEAParts::get('stddob') . " as stddob,
               " . IDEAParts::get('disabcode') . " AS dis,
               " . IDEAParts::get('placecode') . " AS plm,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_teacherstudentassignment ts
             " . IDEAParts::get('studentJoin') . "
	         " . IDEAParts::get('schoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY vouname, stdname
    ";

	$list->addSearchField('Student Ext2 #', 'stdschid');
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFIDEADisability::factory());
	$list->addSearchField(FFIDEAPlacement::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student ID #')->sqlField('stdschid');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('DOB')->sqlField('stddob');
	$list->addColumn('Attending School')->sqlField('vouname');
	$list->addColumn('Disability')->sqlField('dis');
	$list->addColumn('Placement')->sqlField('plm');
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
