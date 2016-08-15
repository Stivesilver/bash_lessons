<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area_id = 113;

	$list = new ListClass();

	$list->title = 'Related Services Dates';

	$list->SQL = "
		SELECT refid,
			   txt01,
			   dat01
		       order_num
		  FROM webset.std_general std
		 WHERE stdrefid = " . $tsRefID . "
		   AND area_id = " . $area_id . "
		 ORDER BY order_num, refid
	";

	$list->addColumn('Related Services');
	$list->addColumn('Date')->type('date');
	$list->addColumn('Order #');

	$list->addURL = CoreUtils::getURL('meet_more_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('meet_more_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_general';
	$list->deleteKeyField = 'refid';

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