<?php

	Security::init();

	CoreUtils::increaseMemory();

	$list = new listClass();
	$list->title = 'Process Coordinator Report By Case Managers';
	$list->showSearchFields = true;
	$list->printable = true;

	$list->getPrinter()
		->printCallback('printTable')
	;

	$state = VNDState::factory()->code;
	$list->SQL = "
		SELECT umpcm.umlastname || ', ' || umpcm.umfirstname AS pcmfullname,
               um.umlastname || ', ' || um.umfirstname AS cmfullname,
               " . IDEAParts::get('stdname') . " AS stdname,
               " . IDEAParts::get('stdcmpltdt') . " AS annualdate,
               " . IDEAParts::get('stdevaldt') . " AS evaldate,
               " . IDEAParts::get('stdtriennialdt') . " AS triennialdate,
               " . IDEAParts::get('stdiepmeetingdt') . " AS stdiepmeetingdt,
               " . IDEAParts::get('stdenrolldt') . " AS stdenrolldt,
               CASE WHEN std.stdrefid IS NULL THEN 0 ELSE 1 END AS stdcounter,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_proccoordmst AS pcm
               INNER JOIN public.sys_usermst AS umpcm ON umpcm.umrefid = pcm.umrefid
               LEFT OUTER JOIN webset.sys_proccoordassignment AS pca ON pca.pcrefid = pcm.pcrefid AND EXISTS(SELECT 1 FROM public.sys_usermst AS um2 WHERE um2.umrefid = pca.cmrefid)
               LEFT OUTER JOIN webset.sys_casemanagermst AS cm ON cm.umrefid = pca.cmrefid
               LEFT OUTER JOIN public.sys_usermst AS um ON um.umrefid = cm.umrefid
               LEFT OUTER JOIN webset.sys_teacherstudentassignment AS ts ON ts.umrefid = cm.umrefid
               " . IDEAParts::get('studentJoin') . "
         WHERE std.vndrefid = VNDREFID
         ADD_SEARCH
		 ORDER BY pcmfullname, cmfullname, stdname
    ";

	$list->addSearchField(FFSelect::factory('Resident District')
			->sql("
				SELECT sys_usermst.umrefid,   sys_usermst.umlastname || ', ' || sys_usermst.umfirstname as fio, 2
				  FROM webset.sys_proccoordmst, public.sys_usermst, public.sys_voumst
				 WHERE public.sys_voumst.vourefid = public.sys_usermst.vourefid
				   AND public.sys_usermst.umrefid = webset.sys_proccoordmst.umrefid
				   AND webset.sys_proccoordmst.vndrefid = VNDREFID
				 ORDER BY 3, fio
		")
			->sqlField('umpcm.umrefid')
	);
	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(FFIDEAStdStatus::factory());
	$list->addSearchField(FFIDEASpEdStatus::factory());

	$list->addColumn('Process Coordinator', '', 'group')
		->sqlField('pcmfullname');
	$list->addColumn('Case Manager', '', 'group')
		->sqlField('cmfullname');
	$list->addColumn('Student')->sqlField('stdname')->width(20);
	$list->addColumn('IEP Meeting')->sqlField('stdiepmeetingdt');
	$list->addColumn('Initiation')->sqlField('stdenrolldt');
	$list->addColumn('Annual')->sqlField('annualdate');
	$list->addColumn('Evaluation')->sqlField('evaldate');
	$list->addColumn('Triennial')->sqlField('triennialdate');
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
