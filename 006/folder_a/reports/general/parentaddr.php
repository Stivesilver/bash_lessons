<?php

	Security::init();

	$list = new listClass();
	$list->title = 'CM Parent Addresses';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->setPageFormat(RCPageFormat::LANDSCAPE)
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('username') . " AS cmfullname,
		       " . IDEAParts::get('stdname') . " AS stdname,
               gl_code,
               stdschid,
               " . IDEAParts::get('disability') . " AS mdisability,
		       " . IDEAParts::get('placement') . " AS plcategory,
               " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdcmpltdt') . " AS annualdate,
		       " . IDEAParts::get('stdevaldt') . " AS evaldate,
		       " . IDEAParts::get('stdtriennialdt') . " AS triennialdate,
               TRIM(COALESCE(gdlnm, '')||', '||COALESCE(gdfnm, ''), ', ') AS gdname,
               COALESCE(TRIM(gdadr1), '') AS gdadr,
               COALESCE(gdcity, '') ||', '||COALESCE(gdstate ||', '||COALESCE(CAST(gdcitycode AS VARCHAR), '')) AS city,
               REPLACE(gdhphn, '() -', '') AS gdhphn,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_casemanagermst AS cm
               INNER JOIN public.sys_usermst AS um ON cm.umrefid = um.umrefid
               INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.umrefid = um.umrefid
             " . IDEAParts::get('studentJoin') . "
	         " . IDEAParts::get('gradeJoin') . "
	         " . IDEAParts::get('schoolJoin') . "
               LEFT OUTER JOIN webset.dmg_guardianmst gm ON gm.stdrefid = std.stdrefid
               LEFT OUTER JOIN webset.def_guardiantype gt ON gm.gdType = gt.gtRefID
         WHERE std.vndrefid = VNDREFID
           ADD_SEARCH
         ORDER BY 1,2, seqnumber, gtrank, UPPER(gdLNm), UPPER(gdFNm)
    ";

	$list->addSearchField(FFIDEACaseManager::factory()->sqlField('ts.umrefid'));
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Case Manager', '', 'group')
		->sqlField('cmfullname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Parent/Guardian')->sqlField('gdname');
	$list->addColumn('Address')->sqlField('gdadr');
	$list->addColumn('City/State/Zip')->sqlField('city');
	$list->addColumn('Home Phone')->sqlField('gdhphn');
	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_casemanagermst')
			->setKeyField('umrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
