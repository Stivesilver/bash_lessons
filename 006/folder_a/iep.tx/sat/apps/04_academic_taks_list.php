<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT trefid,
               to_char(tdate, 'yyyy-mm-dd'),
               subject,
               mastery,
               score
          FROM webset_tx.std_sat_aitaks
         WHERE stdrefid = " . $tsRefID . "
           AND iepyear = " . $stdIEPYear . "
         ORDER BY tdate desc
        ";

	$list->title           = "Texas Assessment of Knowledge and Skills (TAKS)";
	$list->addURL          = CoreUtils::getURL('04_academic_taks_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('04_academic_taks_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_sat_aitaks";
	$list->deleteKeyField  = "trefid";

	$list->addColumn("Date");
	$list->addColumn("Subject");
	$list->addColumn("Total Test Mastery");
	$list->addColumn("Scaled Score")->width('%');

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