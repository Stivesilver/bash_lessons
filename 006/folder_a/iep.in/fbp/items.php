<?php

	Security::init();

	$dskey = io::get('dskey');
	$mode = io::get('mode', TRUE);
	$title = ($mode == 'F' ? 'FBA' : 'BIP');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = $title . ' - Items';

	$list->SQL = "
		SELECT recrefid,
			   bcdesc,
			   bidesc,
			   rectext
		  FROM webset.std_in_bipitems std
			   INNER JOIN webset.disdef_bipitems items ON std.birefid = items.birefid
			   INNER JOIN webset.disdef_bipcat cat ON items.bcrefid = cat.bcrefid
		 WHERE stdrefid = " . $tsRefID . "
		   AND form_type = '" . $mode . "'
		 ORDER BY bcseq, bcdesc, biseq, bidesc
	";

	$list->addColumn('Category')->type('group');
	$list->addColumn('Item');
	$list->addColumn('Narrative');

	$list->addURL = CoreUtils::getURL('items_add.php', array('dskey' => $dskey, 'mode' => $mode));
	$list->editURL = CoreUtils::getURL('items_add.php', array('dskey' => $dskey, 'mode' => $mode));

	$list->deleteTableName = 'webset.std_in_bipitems';
	$list->deleteKeyField = 'recrefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey, 'mode' => $mode))
	);

	$list->printList();
?>