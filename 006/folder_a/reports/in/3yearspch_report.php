<?php

	Security::init();

	$list = new listClass();
	$list->title = '3 year Speech Re-Evals';
	$list->showSearchFields = true;
	$list->printable = true;

	if (io::exists('dt1')) {
		$dt1 = io::get('dt1');
	} else {
		$dt1 = '';
	}

	if (io::exists('dt2')) {
		$dt2 = io::get('dt2');
	} else {
		$dt2 = '';
	}

	$list->SQL = "
		SELECT " . IDEAParts::get('stdname') . " AS stdname,
               gl_code,
               " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('disability') . " AS mdisability,
               TO_CHAR(edncdeval, 'MM/DD/YYYY') as findate,
               " . IDEAParts::get('username') . " AS cmfullname
		  FROM webset.sys_teacherstudentassignment ts
			   LEFT JOIN webset.std_in_eligibility t_3 ON ts.tsrefid = t_3.stdrefid
			   " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
               " . IDEAParts::get('casemanJoin') . "
         WHERE std.vndrefid = VNDREFID
		   AND edncdeval BETWEEN TO_DATE($dt1, 'YYYY-MM-DD') AND TO_DATE($dt2, 'YYYY-MM-DD')
           ADD_SEARCH
         ORDER BY cmfullname, 1
    ";

	$list->addSearchField('Start Date', '', 'date')
		->name('dt1')
		->req();
	$list->addSearchField('End Date', '', 'date')
		->name('dt2')
		->req();
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Case Manager', '', 'group')
		->sqlField('cmfullname');
	$list->addColumn('Student Name')->sqlField('stdname');
	$list->addColumn('Grade Level')->sqlField('gl_code');
	$list->addColumn('Attending School')->sqlField('vouname');
	$list->addColumn('Disability')->sqlField('mdisability');
	$list->addColumn('3 year Speech Re-Eval Date')->sqlField('findate');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>
