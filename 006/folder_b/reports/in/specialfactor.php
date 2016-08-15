<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Special Factors';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->printCallback('printTable')
	;

	$list->SQL = "
		SELECT " . IDEAParts::get('schoolName') . " AS vouname,
		       " . IDEAParts::get('stdname') . " AS stdname,
		       gl_code,
		       sscmnarrative,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
		  FROM webset.std_spconsid
		       INNER JOIN webset.statedef_spconsid_quest ON webset.std_spconsid.scqrefid = webset.statedef_spconsid_quest.scmrefid
		       INNER JOIN webset.statedef_spconsid_answ  ON webset.std_spconsid.scarefid = webset.statedef_spconsid_answ.scarefid
		       INNER JOIN webset.sys_teacherstudentassignment AS ts ON ts.tsrefid = webset.std_spconsid.stdrefid
		       " . IDEAParts::get('studentJoin') . "
		       " . IDEAParts::get('gradeJoin') . "
		       " . IDEAParts::get('schoolJoin') . "
		 WHERE std.vndrefid = VNDREFID
		 ADD_SEARCH
		 ORDER BY vouname, stdname
    ";

	$list->addSearchField(FFIDEASchool::factory());
	$list->addSearchField(FFSelect::factory('Special Factor')
		->sql("
			 SELECT scarefid,
                    scmquestion  || ' -> Answer: ' || scanswer
               FROM webset.statedef_spconsid_answ answ
                    INNER JOIN webset.statedef_spconsid_quest quest ON quest.scmrefid = answ.scmrefid
              WHERE quest.screfid = " . VNDState::factory()->id . "
              ORDER BY seqnum, 2
		")
		->sqlField('webset.std_spconsid.scarefid')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Student', '' , '')->sqlField('vouname');
	$list->addColumn('Student')->sqlField('stdname');
	$list->addColumn('Grade')->sqlField('gl_code');
	$list->addColumn('Narrative')->sqlField('sscmnarrative');
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
