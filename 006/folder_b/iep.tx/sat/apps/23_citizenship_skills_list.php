<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list = new ListClass();
	$list->SQL =
		"SELECT brefid,
                validvalue || COALESCE(' ' || item_other, '') AS vvalue,
                date_beg,
                date_end
           FROM webset_tx.std_sat_beh_prog  std
                INNER JOIN webset.glb_validvalues items  ON item_id = items.refid AND valuename = 'TX_SAT_Beh_Citizen'
          WHERE stdrefid = $tsRefID
            AND iepyear = " . $stdIEPYear . "
            AND area = '3'
          ORDER BY items.glb_enddate desc, items.sequence_number, validvalue
        ";

	$list->title           = "Grade Appropriate Citizenship Skills";
	$list->addURL 	       = CoreUtils::getURL('23_citizenship_skills_edit.php', array('dskey' => $dskey));
	$list->editURL	       = CoreUtils::getURL('23_citizenship_skills_edit.php', array('dskey' => $dskey));
	$list->multipleEdit    = "no";
	$list->deleteTableName = "webset_tx.std_sat_beh_prog";
	$list->deleteKeyField  = "brefid";

	$list->addColumn("Program")->sqlField('vvalue');
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