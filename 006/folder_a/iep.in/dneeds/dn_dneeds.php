<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();
	
	$list->title = 'Determined Needs';

	$list->SQL = "
		SELECT dnrefid,
			   dnseq,
			   dnnarr
		  FROM webset.std_in_dneeds
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY dnseq
	";

	$list->addColumn('Order #', '10%');
	$list->addColumn('Narrative', '90%');

	$list->addURL = CoreUtils::getURL('dn_dneeds_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('dn_dneeds_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_in_dneeds';
	$list->deleteKeyField = 'dnrefid';

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