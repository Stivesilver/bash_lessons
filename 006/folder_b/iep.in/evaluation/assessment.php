<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = 'Evaluation Tests';

	$list->SQL = "
		SELECT shsdrefid,
			   scrdesc,
			   COALESCE(test_name, hspdesc),
			   shsddate,
			   replace(shsdhtmltext, '\n', '<br/>')
		  FROM webset.es_std_scr std
			   INNER JOIN webset.es_statedef_screeningtype ON screenid = scrrefid
			   LEFT OUTER JOIN webset.es_scr_disdef_proc proc ON std.hsprefid = proc.hsprefid
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY scrseq, scrdesc, shsdrefid
	";

	$list->addColumn('Category');
	$list->addColumn('Test');
	$list->addColumn('Date')->type('date');
	$list->addColumn('Results');

	$list->addURL = CoreUtils::getURL('assessment_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('assessment_add.php', array('dskey' => $dskey));
	
	$list->deleteTableName = 'webset.es_std_scr';
	$list->deleteKeyField = 'shsdrefid';

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