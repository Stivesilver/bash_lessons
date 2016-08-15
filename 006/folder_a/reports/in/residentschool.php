<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Resident District Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->printCallback('printTable')
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
               gl_code,
               " . IDEAParts::get('disabcode') . " AS discode,
               " . IDEAParts::get('placecode') . " AS plcode,
               " . IDEAParts::get('schoolName_res') . " AS vouname,
               TO_CHAR(stdcmpltdt, 'MM/DD/YYYY') AS annualdate,
               TO_CHAR(stdtriennialdt, 'MM/DD/YYYY') AS trdate,
               plpgsql_recs_to_str('SELECT sdgname AS column
                                      FROM webset.disdef_stddemogrouping grp
                                           INNER JOIN webset.dmg_studentgroupingdtl dtl ON grp.sdgrefid = dtl.sdgrefid
                                     WHERE dtl.stdrefid = ' || std.stdrefid, ', ') AS sdgname,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment AS ts
               " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
		       " . IDEAParts::get('residJoin') . "
         WHERE std.vndrefid = VNDREFID
         ADD_SEARCH
		 ORDER BY stdname, gl_numeric_value, vouname
    ";

	$list->addSearchField(FFSelect::factory('Resident District')
		->sql("
			 SELECT DISTINCT t03.vndrefid, t03.vndname, 2
			   FROM webset.dmg_studentmst AS t01
			        INNER JOIN webset.sys_teacherstudentassignment AS t02 ON t02.stdrefid = t01.stdrefid
			        INNER JOIN public.sys_vndmst AS t03 ON t03.vndrefid = t01.vndrefid_res
			   WHERE t01.vndrefid = VNDREFID
			   ORDER BY 3, vndname
		")
		->sqlField('std.vndrefid_res')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname')->width(20);
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Resident School')->sqlField('vouname')->width(20);
	$list->addColumn('Disability')->sqlField('discode');
	$list->addColumn('Placement')->sqlField('plcode');
	$list->addColumn('Annual Date')->sqlField('annualdate');
	$list->addColumn('Triennial Date')->sqlField('trdate');
	$list->addColumn('Grouping')->sqlField('sdgname');
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
