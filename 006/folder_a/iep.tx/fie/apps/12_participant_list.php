<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT refid,
			   name,
			   position
		  FROM webset_tx.std_fie_assurance
         WHERE stdrefid = $tsRefID AND iepyear = $stdIEPYear
               ADD_SEARCH
         ORDER BY name
        ";

	$list->title           = "Assurances";
	$list->deleteTableName = "webset_tx.std_fie_assurance";
	$list->deleteKeyField  = "refid";
	$list->addURL          = CoreUtils::getURL('12_participant_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('12_participant_edit.php', array('dskey' => $dskey));

	$list->addColumn("Participant Name")->width('50%');
	$list->addColumn("Position")->width('50%');

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();