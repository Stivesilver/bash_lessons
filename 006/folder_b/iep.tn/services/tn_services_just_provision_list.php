<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Justification for Provision of Service in Environments/Settings not Identified as the Natural Environment';

	$list->SQL = "
		SELECT std.refid,
			   std.txt01,
			   std.txt02,
			   std.txt03
		  FROM webset.std_general AS std
		 WHERE std.stdrefid = " . $tsRefID . "
		   AND std.area_id = " . IDEAAppArea::TN_IFSP_SEVICES . "
		 ORDER BY std.txt01
	";

	$list->addColumn('Service')
		->sqlField('txt01');

	$list->addColumn('Options Considered')
		->sqlField('txt02');

	$list->addColumn('Outcome Not Aachieved Because')
		->sqlField('txt03');

	$list->deleteTableName = 'webset.std_general';
	$list->deleteKeyField  = 'refid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		FFIDEAHelpButton::factory()
			->setHTMLByConstruction(IDEAAppArea::TN_IFSP_SEVICES)
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addURL = CoreUtils::getURL('./tn_services_just_provision_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('./tn_services_just_provision_edit.php', array('dskey' => $dskey));

	$list->printList();
?>
