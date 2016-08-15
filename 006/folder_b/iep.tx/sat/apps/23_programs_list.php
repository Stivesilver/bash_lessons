<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT brefid,
               validvalue || COALESCE(' ' || item_other, ''),
               item_desc,
               to_char(date_beg, 'yyyy-mm-dd'),
               to_char(date_end, 'yyyy-mm-dd')
          FROM webset_tx.std_sat_beh_prog  std
               INNER JOIN webset.glb_validvalues items  ON item_id = items.refid AND valuename = 'TX_SAT_Pos_Proact'
         WHERE stdrefid = $tsRefID
           AND iepyear = " . $stdIEPYear . "
           AND area = 1
         ORDER BY items.glb_enddate desc, items.sequence_number, validvalue
        ";

	$list->title           = "Positive Proactive Discipline";
	$list->addURL          = CoreUtils::getURL('23_programs_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('23_programs_edit.php', array('dskey' => $dskey));
	$list->multipleEdit    = false;
	$list->deleteTableName = "webset_tx.std_sat_beh_prog";
	$list->deleteKeyField  = "brefid";

	$list->addColumn("Program");
	$list->addColumn("Program Description");
	$list->addColumn("Start Date");
	$list->addColumn("End Date");

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