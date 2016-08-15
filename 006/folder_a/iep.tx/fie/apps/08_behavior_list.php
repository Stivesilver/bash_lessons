<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$editUrl    = CoreUtils::getURL('08_behavior_edit.php', array('dskey' => $dskey));
	$list       = new ListClass();

	$list->addURL          = $editUrl;
	$list->editURL         = $editUrl;
	$list->title           = "Adaptive Behavior";
	$list->deleteTableName = "webset_tx.std_fie_adaptivescore";
	$list->deleteKeyField  = "adrefid";
	$list->SQL             = "
		SELECT adrefid,
         	   validvalue,
               score
       	  FROM webset_tx.std_fie_adaptivescore
         INNER JOIN webset.glb_validvalues ON area_id = refid
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
         ORDER BY sequence_number
       ";

	$list->addColumn("Area", "", "text", "", "", "");
	$list->addColumn("Score", "%", "text", "", "", "");

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

?>