<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Course of Study';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " as vouname,
	           " . IDEAParts::get('stdname') . " AS stdname,
		       tsscdesc,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.sys_teacherstudentassignment ts
		       LEFT JOIN webset.std_in_ts AS t2 ON t2.stdrefid = ts.tsrefid
		       LEFT JOIN webset.statedef_ts_studycourse AS t3 ON (t3.tsscrefid::varchar = t2.tsscrefid)
		       " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
		 WHERE std.vndrefid = VNDREFID
		   ADD_SEARCH
		 ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFSelect::factory('Course of Study')
			->sql("
                SELECT tsscrefid,
				   tsscdesc
			  FROM webset.statedef_ts_studycourse
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (enddate IS NULL or now()< enddate)
		     ORDER BY tsscrefid
		")
			->sqlField('t2.tsscrefid')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Course of Study')->sqlField('tsscdesc');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>
