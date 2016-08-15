<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Placement Code Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
		       " . IDEAParts::get('stdname') . " AS stdname,
		       stdschid,
		       stdfedidnmbr,
		       TO_CHAR(siymiepbegdate, 'YYYY') || '-' ||TO_CHAR(siymiependdate, 'YYYY') as iepyear,
		       CASE other != '' WHEN TRUE THEN other ELSE COALESCE(macdesc, '') || ' ' || webset.statedef_mod_acc.stsdesc END as progmod,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.sys_teacherstudentassignment ts
		       INNER JOIN webset.std_in_ed_consid consid ON consid.stdrefid = ts.tsrefid
		       INNER JOIN webset.statedef_mod_acc ON consid.progrefid = webset.statedef_mod_acc.stsrefid
		       LEFT OUTER JOIN webset.statedef_mod_acc_cat ON webset.statedef_mod_acc_cat.macrefid = webset.statedef_mod_acc.macrefid
		       INNER JOIN webset.std_iep_year iep ON ts.tsrefid = iep.stdrefid AND siymcurrentiepyearsw = 'Y'
		       " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
		 WHERE std.vndrefid = VNDREFID
		   AND LOWER(modaccommodationsw) = 'y'
		   ADD_SEARCH
		 ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Attending School', '', 'group')->sqlField('vouname');
	$list->addColumn('Student', '')->sqlField('stdname');
	$list->addColumn('STN#', '')->sqlField('stdschid');
	$list->addColumn('ID#', '')->sqlField('stdfedidnmbr');
	$list->addColumn('IEP Year', '')->sqlField('iepyear');
	$list->addColumn('Program Modification/accommodation')->sqlField('progmod');
	$list->addColumn('Narrative', '')->sqlField('narr');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>
