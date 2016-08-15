<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Form A - Blind and Visually Impaired';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
		       f.brinst,
		       f.bransw,
		       f.methods,
		       f.brbegdt,
		       f.duration,
		       f.brlevel,
		       f.discussed,
		       f.parentansw,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus,
		       ts.tsrefid,
		       f.syrefid
		  FROM webset.std_form_a AS f
		       INNER JOIN webset.sys_teacherstudentassignment AS ts ON f.stdrefid = ts.tsrefid
		       " . IDEAParts::get('studentJoin') . "
		       INNER JOIN webset.std_iep_year iep ON f.syrefid = iep.siymrefid
		   AND siymcurrentiepyearsw = 'Y' 
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY stdlnm, stdfnm
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField('Braille Instruction', 'brinst')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('No Braille factors', 'factors')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Methods', 'methods')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField(FFSwitchYN::factory('Need Braille'))->sqlField('bransw');
	$list->addSearchField(FFSwitchYN::factory('Rehabilitation Services discussed'))->sqlField('discussed');
	$list->addSearchField(FFSwitchYN::factory('Parent Agree'))->sqlField('parentansw');
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Braille Instruction')->sqlField('brinst');
	$list->addColumn('Need Braille')->type('switch')->sqlField('bransw');
	$list->addColumn('No Braille factors')->sqlField('factors');
	$list->addColumn('Methods')->sqlField('methods');
	$list->addColumn('Begin Date')->sqlField('brbegdt')->type('date');
	$list->addColumn('Duration')->sqlField('duration');
	$list->addColumn('Level')->sqlField('brlevel');
	$list->addColumn('RS discussed')->sqlField('discussed')->type('switch');
	$list->addColumn('Parent Agree')->sqlField('parentansw')->type('switch');
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

