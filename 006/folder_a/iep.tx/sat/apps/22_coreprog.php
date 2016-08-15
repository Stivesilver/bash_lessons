<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT prefid,
               aaadesc,
               category_name,
               CASE WHEN lower(item_name)='other' THEN item_other ELSE item_name END,
               to_char(program_date, 'yyyy-mm-dd'),
               to_char(program_end, 'yyyy-mm-dd')
          FROM webset_tx.std_sat_coreprog  std
               INNER JOIN webset_tx.def_sat_program_item items  ON item_id = items.refid
               INNER JOIN webset_tx.def_sat_program_cat cat ON category_id = cat.refid
               INNER JOIN webset.statedef_assess_acc ON  subject_id = aaarefid
         WHERE stdrefid = " . $tsRefID . "
           AND iepyear = " . $stdIEPYear . "
         ORDER BY items.enddate desc, aaadesc, cat.seqnum, items.seqnum, item_name
        ";

	$list->title = "Core Program ";

	$list->addColumn("Subject");
	$list->addColumn("Category");
	$list->addColumn("Program");
	$list->addColumn("Start Date");
	$list->addColumn("End Date");

	$list->addURL          = CoreUtils::getURL('22_coreprog_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('22_coreprog_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_sat_coreprog";
	$list->deleteKeyField  = "prefid";

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