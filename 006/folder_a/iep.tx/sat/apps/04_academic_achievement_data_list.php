<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->SQL = "
		SELECT arefid,
               to_char(asdate, 'yyyy-mm-dd'),
               testname,
               subjarea,
               score
          FROM webset_tx.std_sat_aidata
         WHERE stdrefid = $tsRefID
           AND iepyear = " . $stdIEPYear . "
         ORDER BY asdate desc
        ";

	$list->title = "Achievement Test Data ";

	$list->addColumn("Date");
	$list->addColumn("Name of Test");
	$list->addColumn("Subject Area");
	$list->addColumn("Score")->width('%');

	$list->addURL          = CoreUtils::getURL('04_academic_achievement_data_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('04_academic_achievement_data_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_sat_aidata";
	$list->deleteKeyField  = "arefid";

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