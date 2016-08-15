<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Badges Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE);

	$list->SQL = "
		SELECT tsrefid,
		       stdlnm,
		       stdfnm,
		       stdmnm,
		       " . IDEAParts::get('schoolName') . " as vouname,
		       " . IDEAParts::get('username') . " as username,
		       gl_code,
		       std.stdrefid,
		       std.stdstateidnmbr,
		       std.stdschid,
		       std.externalid,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.sys_teacherstudentassignment ts
		     " . IDEAParts::get('studentJoin') . "
	         " . IDEAParts::get('casemanJoin') . "
	         " . IDEAParts::get('schoolJoin') . "
             " . IDEAParts::get('gradeJoin') . "
             " . IDEAParts::get('enrollJoin') . "
		 WHERE std.vndrefid = VNDREFID
		       ADD_SEARCH
		 ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
    ";

	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('username'));
	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField(FFSwitchYN::factory('504 Student'), 'student504')->value('Y');
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn("Last Name", "", "text", "", "", "");
	$list->addColumn("First Name", "", "text", "", "", "");
	$list->addColumn("Middle Name", "", "text", "", "", "");
	$list->addColumn("Attending School", "", "text", "", "", "");
	$list->addColumn("Teacher", "", "text", "", "", "");
	$list->addColumn("Grade", "", "text", "", "", "");
	$list->addColumn("Lumen Student ID #", "", "text", "", "", "");
	$list->addColumn("State Student ID #", "", "text", "", "", "");
	$list->addColumn("External ID #", "", "text", "", "", "");
	$list->addColumn("External ID (Ext2) #", "", "text", "", "", "");
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
