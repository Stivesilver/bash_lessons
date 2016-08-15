<?php

	Security::init();

	$area       = 2;
	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();
	$SQL        = "
		SELECT a_name
	      FROM webset_tx.def_fie_academic
	     WHERE a_refid = $area
        ";

	$area = db::execSQL($SQL)->getOne();

	$list->showSearchFields = true;
	$list->addURL           = CoreUtils::getURL('07_weakness_edit.php', array('dskey' => $dskey));
	$list->editURL          = CoreUtils::getURL('07_weakness_edit.php', array('dskey' => $dskey));
	$list->deleteTableName  = "webset_tx.std_fie_academic";
	$list->deleteKeyField   = "refid";
	$list->SQL              = "
		SELECT refid,
			   strength,
			   weakness
		  FROM webset_tx.std_fie_academic
		 WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
           AND a_refid = 2
               ADD_SEARCH
         ORDER BY refid
        ";

	$list->title = "$area Strengths/Weaknesses";

	$list->addColumn("Strength");
	$list->addColumn("Weakness");

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

?>