<?php

	Security::init();

	$encodvalarr = db::execSQL("
		SELECT denrefid
		  FROM webset.disdef_enroll_codes district
		       INNER JOIN webset.statedef_enroll_codes state ON state.enrrefid = district.statecode_id
		 WHERE vndrefid = VNDREFID
		   AND COALESCE(district.enddate, now()) >= now()
		 ORDER BY district.seqnum, dencode
	")->indexCol(0);
	$encodvalstr = implode(',', $encodvalarr);

	$list = new listClass();
	$list->title = 'Sp Ed Students by Date';
	$list->showSearchFields = true;
	$list->checkBoxColumn = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT stdschid,
		       stdlnm,
		       stdfnm,
		       umlastname,
		       umfirstname,
		       vouname,
		       gl_code,
		       gl_refid,
		       vourefid,
		       disability,
		       stdstatus,
		       enrollcode,
		       exitcode,
		       stdenterdt,
		       denrefid,
		       stdexitdt,
		       exit_id,
		       vndrefid
		  FROM (
				SELECT stdschid,
					   stdlnm,
					   stdfnm,
					   umlastname,
					   umfirstname,
					   vouname,
					   CASE std.gl_refid IS NULL
					   WHEN TRUE THEN '<FONT color=red><B>No Data'
					   ELSE gl_code
					   END,
					   std.gl_refid,
					   std.vourefid,
					   (
						SELECT webset.statedef_disablingcondition.dcdesc
						  FROM webset.std_disabilitymst
						       INNER JOIN webset.statedef_disablingcondition ON webset.statedef_disablingcondition.dcrefid = webset.std_disabilitymst.dcrefid
						 WHERE webset.std_disabilitymst.stdrefid = ts.tsrefid
						   AND webset.std_disabilitymst.sdtype = 1 LIMIT 1
					   ) AS disability,
					   CASE
					   WHEN " . IDEAParts::get('stdActive') . " THEN 'A'
					   ELSE 'I'
					   END AS stdstatus,
					   COALESCE(dencode || ' - ', '') || dendesc AS enrollcode,
					   COALESCE(dexcode || ' - ', '') || dexdesc AS exitcode,
					   stdenterdt,
					   ts.denrefid,
					   stdexitdt,
					   COALESCE(ts.dexrefid, -333) AS exit_id,
					   std.vndrefid
				  FROM webset.sys_teacherstudentassignment ts 
					   " . IDEAParts::get('studentJoin') . " 
					   " . IDEAParts::get('casemanJoin') . " 
					   " . IDEAParts::get('gradeJoin') . " 
					   " . IDEAParts::get('repschJoin') . " 
					   " . IDEAParts::get('enrollJoin') . " 
					   " . IDEAParts::get('exitJoin') . "
		       ) AS t
		 WHERE vndrefid = VNDREFID
		 ORDER BY UPPER(stdlnm), UPPER(stdfnm)
    ";

	$list->addSearchField('Report Date', '', 'date')
		->value(date('Y-m-d'))
		->sqlField("
			COALESCE(stdenterdt, to_date('1000-01-01', 'YYYY-MM-DD')) <= ADD_VALUE
			AND ADD_VALUE <= COALESCE(stdexitdt, TO_DATE('3000-01-01', 'YYYY-MM-DD'))
		");
	
	$list->addSearchField('Sp Ed Enrollment Code', 'denrefid', 'select_check')
		->sql("
			SELECT denrefid,
				   COALESCE(dencode || ' - ','') || dendesc AS enrollcode
			  FROM webset.disdef_enroll_codes district
				   INNER JOIN webset.statedef_enroll_codes state ON state.enrrefid = district.statecode_id
			 WHERE vndrefid = VNDREFID
			   AND COALESCE(district.enddate, now()) >= now()
			 ORDER BY district.seqnum, dencode
		")
		->selectAll()
		->breakRow();

	$list->addSearchField('Sp Ed Exit Code', "exit_id", 'select_check')
		->sql("
			SELECT -333, 'Blank Exit Code'
			 UNION All
			SELECT dexrefid,
				   COALESCE(dexcode || ' - ','') || dexdesc AS exitcode
			  FROM webset.disdef_exit_codes district
				   LEFT OUTER JOIN webset.statedef_exitcategories state ON state.secrefid = district.statecode_id
			 WHERE vndrefid = VNDREFID
			   AND COALESCE(district.enddate, now()) >= now()
			   AND COALESCE(state.recdeactivationdt, now()) >= now()
			 ORDER BY 1
		")
		->selectAll()
		->breakRow();

	$list->addSearchField(FFIDEASchool::factory(true))->sqlField('vourefid');
	$list->addSearchField(FFIDEAGradeLevel::factory()->sqlField('gl_refid'));
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());

	$list->addColumn('ID')->sqlField('stdschid');
	$list->addColumn('Student LN')->sqlField('stdlnm');
	$list->addColumn('Student FN')->sqlField('stdfnm');
	$list->addColumn('Attending School')->sqlField('vouname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('CM LN')->sqlField('umlastname');
	$list->addColumn('CM FN')->sqlField('umfirstname');
	$list->addColumn('Primary Exceptionality')->sqlField('disability');
	$list->addColumn('Sp Ed Enrollment Date', '', 'date')->sqlField('stdenterdt')->printable(false);
	$list->addColumn('Sp Ed Enrollment Code')->sqlField('enrollcode')->printable(false);
	$list->addColumn('Sp Ed Exit Date', '', 'date')->sqlField('stdexitdt')->printable(false);
	$list->addColumn('Sp Ed Exit Code')->sqlField('exitcode')->printable(false);
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.dmg_studentmst')
			->setKeyField('stdschid')
			->applyListClassMode()
	);

	$list->printList();
?>
