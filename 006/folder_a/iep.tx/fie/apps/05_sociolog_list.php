<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();
	$list->SQL  = "
		SELECT refid,
               s_src,
               s_date
          FROM webset_tx.std_fie_social
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
           AND apptype = '5'
         ORDER BY refid desc
        ";

	$list->title           = "Sociological";
	$list->addURL          = CoreUtils::getURL('05_sociolog_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('05_sociolog_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_fie_social";
	$list->deleteKeyField  = "refid";

	$list->addColumn("Sources of Data");
	$list->addColumn("Date")
		->type('date')
		->width('%');

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