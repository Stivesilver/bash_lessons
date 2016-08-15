<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT lrefid,
               subject,
               score
          FROM webset_tx.std_sat_aisubjects std
         WHERE stdrefid = " . $tsRefID . "
           AND iepyear = " . $stdIEPYear . "
         ORDER BY lrefid desc
        ";

	$list->title = "Subjects and Current Grades";

	$list->addColumn("Subject");
	$list->addColumn("Score")->width('%');

	$list->addURL          = CoreUtils::getURL('04_academic_subjects_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('04_academic_subjects_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_sat_aisubjects";
	$list->deleteKeyField  = "lrefid";

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