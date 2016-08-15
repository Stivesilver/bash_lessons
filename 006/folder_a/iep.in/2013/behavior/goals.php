<?php

	Security::init();

	$dskey = io::get('dskey');
	$tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Specific Behavior Goals';

	$list->SQL = "
		SELECT grefid, goal, action_plan
		  FROM webset.std_in_bipgoals
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY grefid
    ";

	$list->addColumn('Specific Behavior Goal');
	$list->addColumn('Plan of Action');

	$list->addURL = CoreUtils::getURL('goals_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('goals_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_in_bipgoals';
	$list->deleteKeyField = 'grefid';

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
