<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Form C - Transition Services';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
		       area.tadesc AS area,
		       f.postgoals,
		       f.tsdesc,
		       f.tr_school,
		       f.tsdesc_std,
		       f.tr_student,
		       f.tsdesc_par,
		       f.tr_parent,
		       f.tsdesc_agn,
		       f.tr_agency,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus
		  FROM webset.std_form_c_serv AS f
		       INNER JOIN webset.std_iep_year AS iep ON f.syrefid = iep.siymrefid
		       INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.tsrefid = iep.stdrefid AND siymcurrentiepyearsw = 'Y'
			   " . IDEAParts::get('studentJoin') . "
               INNER JOIN webset.statedef_transarea AS area ON f.tarefid = area.tarefid
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY stdlnm, stdfnm, area.seqnum, tadesc
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField('Area', 'f.tarefid', 'select')
		->sql("
			SELECT tarefid,
			       tadesc 
			  FROM webset.statedef_transarea area
			 WHERE (enddate IS NULL OR now()< enddate)
			 ORDER BY seqnum 
		");
	$list->addSearchField('Postsecondary Goals', 'postgoals')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Service #1', 'tsdesc')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Service #2', 'tsdesc_std')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Service #3', 'tsdesc_par')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Service #4', 'tsdesc_agn')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Area')->sqlField('area');
	$list->addColumn('Postsecondary Goals')->sqlField('postgoals');
	$list->addColumn('Service #1')->sqlField('tsdesc');
	$list->addColumn('Responsible School')->sqlField('tr_school');
	$list->addColumn('Service #2')->sqlField('tsdesc_std');
	$list->addColumn('Responsible Student')->sqlField('tr_student');
	$list->addColumn('Service #3')->sqlField('tsdesc_std');
	$list->addColumn('Responsible Parent')->sqlField('tr_parent');
	$list->addColumn('Service #4')->sqlField('tsdesc_agn');
	$list->addColumn('Responsible Agency')->sqlField('tr_agency');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>


