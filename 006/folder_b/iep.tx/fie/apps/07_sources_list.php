<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->showSearchFields = true;
	$list->addURL           = CoreUtils::getURL('07_sources_edit.php', array('dskey' => $dskey));
	$list->editURL          = CoreUtils::getURL('07_sources_edit.php', array('dskey' => $dskey));
	$list->deleteTableName  = "webset_tx.std_fie_social";
	$list->deleteKeyField   = "refid";
	$list->title            = "Social/Emotional";
	$list->SQL              = "
		SELECT refid,
			   s_src,
			   s_date
          FROM webset_tx.std_fie_social
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
           AND apptype = '7'
               ADD_SEARCH
         ORDER BY refid desc, s_src
         ";

	$list->addColumn("Sources of Data")->width('90%');
	$list->addColumn("Date")
		->type('date')
		->width('10%');

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