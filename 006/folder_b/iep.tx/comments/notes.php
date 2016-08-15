<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Notices';

	$list->SQL = "
		SELECT siairefid,
			   notes
		  FROM webset_tx.std_notes
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
		 ORDER BY siairefid
	";

	$list->addColumn('Text');

	$list->addURL = CoreUtils::getURL('notes_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('notes_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset_tx.std_notes';
	$list->deleteKeyField = 'siairefid';

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