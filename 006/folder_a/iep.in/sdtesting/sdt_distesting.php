<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = 'District Testing';

	$list->SQL = "		
		SELECT t0.sitdrefid,
			   t2.ditmdesc,
			   sitddt,
			   t1.ditddesc,
			   COALESCE(t0.sitdscore, '__') || ' ' || COALESCE(t1.ditdscore, '__'),
			   t3.ditrdesc
		  FROM webset.std_in_test_dis AS t0
			   INNER JOIN webset.disdef_in_testdtl AS t1 ON t1.ditdrefid = t0.ditdrefid
			   INNER JOIN webset.disdef_in_testmst AS t2 ON t2.ditmrefid = t1.ditmrefid
			   INNER JOIN webset.disdef_in_test_rating AS t3 ON t3.ditrrefid = t0.ditrrefid
		 WHERE t0.stdrefid = " . $tsRefID . "
		 ORDER BY sitddt, t1.ditmrefid, t0.ditdrefid
	";

	$list->addColumn('Test Name', '27%');
	$list->addColumn('Test Date', '10%')->type('date');
	$list->addColumn('Test Detail Name', '27%');
	$list->addColumn('Test Detail Score', '15%');
	$list->addColumn('Test Detail Rating', '21%');

	$list->addURL = CoreUtils::getURL('sdt_distesting_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('sdt_distesting_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_in_test_dis';
	$list->deleteKeyField = 'sitdrefid';

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