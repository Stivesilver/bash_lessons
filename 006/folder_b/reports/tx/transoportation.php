<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Transoportation';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
	           gl_code as grade,
	           " . IDEAParts::get('schoolName') . " AS vouname,
               regchk.srkeydata AS rchk,
               CASE WHEN regf.srkeydata != '' AND regchk.srkeydata = 'Y' THEN regf.lastuser ELSE '' END AS lastuser,
               CASE WHEN regf.srkeydata != '' AND regchk.srkeydata = 'Y' THEN regf.lastupdate ELSE null END AS lastupdate,
               CASE WHEN regf.srkeydata != '' AND regchk.srkeydata = 'Y' THEN 'Y' ELSE 'N' END AS rform,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
	      FROM webset.sys_teacherstudentassignment ts
		       " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
	           " . IDEAParts::get('schoolJoin') . "
		       LEFT JOIN webset.std_iep_year AS iepyear ON ts.tsrefid = iepyear.stdrefid AND siymcurrentiepyearsw = 'Y'
		       LEFT JOIN webset.std_registry AS regchk ON (regchk.stdrefid = ts.tsrefid
								                   AND regchk.srkeygroup = 'tx_iep'
								                   AND regchk.srkeyname = 'Transportation_chk'
								                   AND regchk.iep_year = siymrefid)
			   LEFT JOIN webset.std_registry AS regf ON (regf.stdrefid = ts.tsrefid
								                   AND regf.srkeygroup = 'tx_iep'
								                   AND regf.srkeyname = 'Transportation_form'
								                   AND regf.iep_year = siymrefid)

	     WHERE std.vndrefid = VNDREFID
	       ADD_SEARCH
	     ORDER BY grade, stdname
    ";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAGradeLevel::factory())->sqlField('std.gl_refid');
	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFSwitchYN::factory('Transoportation'))->sqlField('regchk.srkeydata');
	$list->addSearchField(FFSwitchYN::factory('Completed Form'))->sqlField("CASE WHEN regf.srkeydata != '' AND regchk.srkeydata = 'Y' THEN 'Y' ELSE 'N' END");
	$list->addSearchField('Form Completed by')->sqlField("CASE WHEN regf.srkeydata != '' AND regchk.srkeydata = 'Y' THEN regf.lastuser ELSE '' END");
	$list->addSearchField('Form Completed when', '', 'daterange')->sqlField("CASE WHEN regf.srkeydata != '' AND regchk.srkeydata = 'Y' THEN regf.lastupdate ELSE null END ");
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());



	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('grade');
	$list->addColumn('School Name')->sqlField('vouname');
	$list->addColumn('Transporation')->sqlField('rchk')->type('switch');
	$list->addColumn('Completed Form')->sqlField('rform')->type('switch');
	$list->addColumn('Form Completed by')->sqlField('lastuser');
	$list->addColumn('Form Completed when')->sqlField('lastupdate')->type('date');


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
