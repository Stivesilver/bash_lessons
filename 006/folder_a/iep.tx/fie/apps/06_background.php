<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->showSearchFields = true;
	$list->title            = "Cultural, Linguistic, and Experiential Backgrounds";
	$list->addURL          = CoreUtils::getURL('06_background_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('06_background_edit.php', array('dskey' => $dskey));
	$list->deleteTableName  = "webset_tx.std_fie_bground";
	$list->deleteKeyField   = "refid";
	$list->SQL              = "
		SELECT refid,
			   CASE WHEN LOWER(b_name) LIKE '%other%' THEN b_name || ' ' || other ELSE b_name END
		  FROM webset_tx.std_fie_bground
		 INNER JOIN webset_tx.def_fie_bground USING (b_refid)
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
               ADD_SEARCH
         ORDER BY b_name
        ";


	$list->addColumn("Background")->width('100%');

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