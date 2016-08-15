<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Placement Code Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " as vouname,
	           " . IDEAParts::get('stdname') . " AS stdname,
		       COALESCE(plmdef.silclrecode, '')||COALESCE(' - '||plmdef.silcmaindesc, '') AS stdplm,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.sys_teacherstudentassignment ts
		       INNER JOIN webset.std_in_lre_selections AS plm ON plm.stdrefid = ts.tsrefid
		       INNER JOIN webset.statedef_in_lre_codes AS plmdef ON plmdef.silcrefid = plm.silcrefid AND plmdef.silcearlychildhoodsw  = ts.stdearlychildhoodsw
		       INNER JOIN webset.statedef_in_lre_selection_codes AS sc ON sc.silsclreselectioncode = plm.silsclreselectioncode AND sc.silscrejectioncodesw != 'Y'
		       " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
		 WHERE std.vndrefid = VNDREFID
		   ADD_SEARCH
		 ORDER BY stdplm, vouname, stdname
    ";

	$list->addSearchField(FFSelect::factory('Placement')
			->sql("
                SELECT silcrefid, COALESCE(pd.silclrecode, '')||COALESCE(' - '||pd.silcmaindesc, ''), 2
                  FROM webset.statedef_in_lre_codes AS pd
                 ORDER BY 3
		")
			->sqlField('plmdef.silcrefid')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('', '', 'group')->sqlField('stdplm');
	$list->addColumn('Student', '')->sqlField('stdname');

	$list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
	$list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

	$list->printList();
?>
