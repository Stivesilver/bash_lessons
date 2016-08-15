<?php

	Security::init();
	$set_ini = IDEAFormat::getIniOptions();

	$dskey = io::get('dskey');
	$tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

	$list = new ListClass();

	$list->SQL = "
        SELECT siairefid,
			   siaitext
	  	  FROM webset.std_additionalinfo
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY siairefid
    ";

	$list->title = $set_ini['iep_additional_info_title'];

	$list->addColumn($set_ini['iep_additional_info_title']);

	$list->addURL = CoreUtils::getURL('srv_addinfo_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('srv_addinfo_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_additionalinfo';
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