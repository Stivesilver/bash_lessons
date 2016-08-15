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
               weekly,
               roleplay,
               to_char(date_beg, 'yyyy-mm-dd'),
               to_char(date_end, 'yyyy-mm-dd')
          FROM webset_tx.std_sat_beh_prog  std
               INNER JOIN webset.glb_validvalues items  ON item_id = items.refid AND valuename = 'TX_SAT_Beh_Social'
         WHERE stdrefid = $tsRefID
           AND iepyear = " . $stdIEPYear . "
           AND area = '4'
         ORDER BY items.glb_enddate desc, items.sequence_number, validvalue
        ";

	$list->title = "Social Skills Training";

	$list->addColumn("Program");
	$list->addColumn("Weekly Skill");
	$list->addColumn("Role Play/ Modeling");
	$list->addColumn("Start Date");
	$list->addColumn("End Date", "", "text", "", "", "");

	$list->addURL 	       = CoreUtils::getURL('23_social_skills_edit.php', array('dskey' => $dskey));
	$list->editURL	       = CoreUtils::getURL('23_social_skills_edit.php', array('dskey' => $dskey));
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