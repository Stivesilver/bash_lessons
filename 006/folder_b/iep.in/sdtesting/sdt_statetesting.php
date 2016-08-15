<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = 'State Testing';

	$list->SQL = "
		SELECT t0.sitsrefid,
			   t2.sitmdesc,
			   sitsdt,
			   t1.sitddesc,
			   COALESCE(t0.sitsscore, '__'),
			   t3.sitrdesc
		  FROM webset.std_in_test_state AS t0
			   INNER JOIN webset.statedef_in_testdtl AS t1 ON t1.sitdrefid = t0.sitdrefid
			   INNER JOIN webset.statedef_in_testmst AS t2 ON t2.sitmrefid = t1.sitmrefid
			   INNER JOIN webset.statedef_in_test_rating AS t3 ON t3.sitrrefid = t0.sitrrefid
		 WHERE t0.stdrefid = " . $tsRefID . "
		 ORDER BY sitsdt, t1.sitmrefid, t0.sitdrefid

	";

	$list->addColumn('Test Name', '27%');
	$list->addColumn('Test Date', '10%')->type('date');
	$list->addColumn('Test Detail Name', '27%');
	$list->addColumn('Test Detail Score', '15%');
	$list->addColumn('Test Detail Rating', '21%');

	$list->addURL = CoreUtils::getURL('sdt_statetesting_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('sdt_statetesting_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_in_test_state';
	$list->deleteKeyField = 'sitsrefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();
?>