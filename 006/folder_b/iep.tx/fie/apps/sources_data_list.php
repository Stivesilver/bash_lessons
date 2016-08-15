<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->get('stdIEPYear');
	$tsRefID    = $ds->get('tsRefID');
	$editUrl    = CoreUtils::getURL('sources_data_edit.php', array('dskey' => $dskey));
	$list       = new ListClass();

	$list->title           = "Language/Communicative Status";
	$list->addURL          = $editUrl;
	$list->editURL         = $editUrl;
	$list->deleteTableName = "webset_tx.std_fie_social";
	$list->deleteKeyField  = "refid";

	$list->SQL = "
		SELECT refid,
               s_src,
               s_date
          FROM webset_tx.std_fie_social
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
           AND apptype = '1'
         ORDER BY refid desc
        ";

	$list->addColumn("Sources of Data");
	$list->addColumn("Date")
		->type('date')
		->width('%');

	$list->addButton(
		IDEAFormat::getPrintButton(array('tsRefID' => $tsRefID))
	);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

?>