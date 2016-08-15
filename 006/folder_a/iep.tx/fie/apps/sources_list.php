<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$editUrl    = CoreUtils::getURL('sources_edit.php', array('dskey' => $dskey));
	$list       = new ListClass();

	$list->title           = "Physical (including motor abilities)";
	$list->addURL          = $editUrl;
	$list->editURL         = $editUrl;
	$list->deleteTableName = "webset_tx.std_fie_social";
	$list->deleteKeyField  = "refid";
	$list->SQL             = "
		SELECT refid,
               s_src,
               s_date
          FROM webset_tx.std_fie_social
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
           AND apptype = '2'
         ORDER BY refid desc
        ";

	$list->addColumn("Sources of Data")
		->sqlField('s_src');

	$list->addColumn("Date")
		->type('date')
		->sqlField('s_date')
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