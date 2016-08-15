<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Placement Code Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " as vouname,
		       " . IDEAParts::get('stdname') . " AS stdname,
		       plmdef.spccode AS stdplm,
		       gl_code,
		       " . IDEAParts::get('username') . " as cmanager,
             CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
             CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.sys_teacherstudentassignment ts
		       INNER JOIN webset.std_placementcode AS plm ON plm.stdrefid = ts.tsrefid
		       INNER JOIN webset.statedef_placementcategorycode AS plmdef ON plmdef.spcrefid = plm.spcrefid
		       " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
               " . IDEAParts::get('casemanJoin') . "
		 WHERE std.vndrefid = VNDREFID
		 ADD_SEARCH
		 ORDER BY stdplm, vouname, stdname, gl_code
    ";

	$list->addSearchField(FFSelect::factory('Placement')
			->sql("
			SELECT spcrefid, CASE WHEN LENGTH(spccode || ' - ' || spcdesc) > 50 THEN SUBSTR(spccode || ' - ' || spcdesc, 1, 50) || '...' ELSE spccode || ' - ' || spcdesc END || ' (' || CASE spctcode WHEN 'EC' THEN 'EC' ELSE 'K-12' END || ')', 2
			  FROM webset.statedef_placementcategorycode plc
			       INNER JOIN webset.statedef_placementcategorycodetype ec ON	plc.spctRefID = ec.spctRefID
			 WHERE plc.screfid = " . VNDState::factory()->id . "
		       AND (plc.recdeactivationdt IS NULL or now()< plc.recdeactivationdt)
	         ORDER BY 3, spccode
		")
			->sqlField('plm.spcrefid')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Placement Code', '', 'group')->sqlField('stdplm');
	$list->addColumn('Student', '')->sqlField('stdname');
	$list->addColumn('Grade', '')->sqlField('gl_code');
	$list->addColumn('Case Manager', '')->sqlField('cmanager');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>
