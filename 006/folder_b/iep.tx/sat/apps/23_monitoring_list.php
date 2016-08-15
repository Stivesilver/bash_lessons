<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT brefid,
               documentation,
               to_char(date_beg, 'yyyy-mm-dd'),
               to_char(date_end, 'yyyy-mm-dd')
          FROM webset_tx.std_sat_beh_prog  std
         WHERE stdrefid = $tsRefID
           AND iepyear = " . $stdIEPYear . "
           AND area = '5'
         ORDER BY brefid
        ";

	$list->title = "Active Supervision And Monitoring";

	$list->addColumn("Documentation");
	$list->addColumn("Start Date");
	$list->addColumn("End Date");

	$list->addURL 	       = CoreUtils::getURL('23_monitoring_edit.php', array('dskey' => $dskey));
	$list->editURL	       = CoreUtils::getURL('23_monitoring_edit.php', array('dskey' => $dskey));
	$list->multipleEdit    = "no";
	$list->deleteTableName = "webset_tx.std_sat_beh_prog";
	$list->deleteKeyField  = "brefid";

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