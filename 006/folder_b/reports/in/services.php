<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Services and Frequency';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->printCallback('printTable')
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
	           gl_code,
	           CASE lower(stsDesc) WHEN 'other' THEN stsDesc || COALESCE(': ' || stsother, '') ELSE stsDesc END AS service,
	           ss.ssmTime || '  ' || ad.sadesc || ' ' || fd.sfdesc AS frequency,
	           " . IDEAParts::get('schoolName') . " AS vouname,
	           " . IDEAParts::get('stdiepmeetingdt') . " as meetdate,
	           TO_CHAR(ssmBegDate, 'mm/dd/yyyy') || ' - ' ||TO_CHAR(ssmEndDate, 'mm/dd/yyyy') as duration,
	           CASE  WHEN LOWER(loc.crtdesc) LIKE (LOWER('%other%')) THEN 'Other: ' || ss.loc_oth  ELSE loc.crtdesc END as location,
	           tsndesc,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
		       CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_teacherstudentassignment AS ts
	           LEFT OUTER JOIN webset.std_srv_sped ss 					ON ss.stdrefid = ts.tsrefid
	           INNER JOIN webset.statedef_services_all ssd 				ON ss.stsrefid = ssd.stsrefid
	           LEFT OUTER JOIN webset.def_spedfreq fd 					ON fd.sfrefid = ss.ssmfreq
	           LEFT OUTER JOIN webset.def_spedamt ad 					ON ad.sarefid = ss.ssmamt
	           LEFT OUTER JOIN webset.def_classroomtype loc 			ON ss.ssmClassType = loc.crtRefID
	           LEFT OUTER JOIN webset.disdef_tsn tsn 					ON ss.srv_class = tsn.tsnrefid
	           " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
		       " . IDEAParts::get('schoolJoin') . "
	     WHERE std.vndrefid = VNDREFID
	     ADD_SEARCH
	     ORDER BY vouname, stdname, bcpseqnumber, ssmbegdate
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFSelect::factory('Service')
			->sql("
				 SELECT 0, 'All' as tsndesc, 1
	              UNION
	             SELECT stsrefid, COALESCE(stscode, ' ')||' - '||stsdesc AS stsdescr, 2
	               FROM webset.statedef_services_all
	              WHERE screfid = " . VNDState::factory()->id . "
	                AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
	              ORDER BY 3, 2
			")
			->sqlField('ssd.stsrefid')
	);
	$list->addSearchField(FFSelect::factory('Service Area')
			->sql("
				 SELECT 0, ' All' as tsndesc, 1
                  UNION
                 SELECT tsnrefid, tsndesc, 2
                   FROM webset.disdef_tsn
                  WHERE vndrefid = VNDREFID
                  ORDER BY 3, tsndesc
			")
			->sqlField('tsnrefid')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('School', '', 'group')->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('IEP Date')->sqlField('meetdate');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Type')->sqlField('service');
	$list->addColumn('Service Area')->sqlField('tsndesc');
	$list->addColumn('Frequency')->sqlField('frequency');
	$list->addColumn('Location')->sqlField('location');
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
