<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT brefid,
               prule,
               responce_pos,
               responce_cor,
               date_beg,
               date_end
          FROM webset_tx.std_sat_beh_prog  std
         WHERE stdrefid = $tsRefID
           AND iepyear = " . $stdIEPYear . "
           AND area = '2'
         ORDER BY brefid
        ";

	$list->title           = "Campus Rules";
	$list->addURL 	       = CoreUtils::getURL('23_campus_rules_edit.php', array('dskey' => $dskey));
	$list->editURL	       = CoreUtils::getURL('23_campus_rules_edit.php', array('dskey' => $dskey));
	$list->multipleEdit    = "no";
	$list->deleteTableName = "webset_tx.std_sat_beh_prog";
	$list->deleteKeyField  = "brefid";

	$list->addColumn("Rule")->sqlField('prule');
	$list->addColumn("Positive Responses")->sqlField('responce_pos');
	$list->addColumn("Corrective Responses")->sqlField('responce_cor');
	$list->addColumn("Start Date")->sqlField('date_beg')->type('date');
	$list->addColumn("End Date")->sqlField('date_end')->type('date');

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