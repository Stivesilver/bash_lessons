<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Form C - Graduation';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
			   ARRAY_TO_STRING(
				   ARRAY(
					   SELECT validvalue
						 FROM webset.glb_validvalues
						WHERE valuename = 'MO_FormC_Grad'
						  AND ',' || f.graduate || ',' LIKE '%,' || validvalueid::varchar || ',%'
						ORDER BY sequence_number
				   ), 
				   ', ' 
			   ) AS graduateby,
			   f.timegrad,
		       CASE
		       WHEN " . IDEAParts::get('stdActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS stdstatus,
		       CASE
		       WHEN " . IDEAParts::get('spedActive') . " THEN 'Y'
		       ELSE 'N'
		       END AS spedstatus
		  FROM webset.std_form_c AS f
		       INNER JOIN webset.std_iep_year AS iep ON f.syrefid = iep.siymrefid
		       INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.tsrefid = iep.stdrefid AND siymcurrentiepyearsw = 'Y'
			   " . IDEAParts::get('studentJoin') . "
		 WHERE std.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY stdlnm, stdfnm 
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEASchool::factory())->name('vourefid');
	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('std.gl_refid'));
	$list->addSearchField('Graduate by', "',' || f.graduate || ',' LIKE '%,ADD_VALUE,%'", 'select')
		->sql("
			SELECT validvalueid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE ValueName = 'MO_FormC_Grad'
			 ORDER BY sequence_number
		");
	$list->addSearchField('Month and Year of Graduation', 'timegrad')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Graduate by')->sqlField('graduateby');
	$list->addColumn('Anticipated month and year of graduation')->sqlField('timegrad');
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


