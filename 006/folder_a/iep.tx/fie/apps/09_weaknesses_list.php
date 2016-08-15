<?php

	Security::init();

	$area       = 1;
	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT refid,
		       strength,
		       weakness
	   	  FROM webset_tx.std_fie_academic
		 WHERE stdrefid = $tsRefID
   		   AND iepyear = $stdIEPYear
       	   AND a_refid = $area
               ADD_SEARCH
     	 ORDER BY refid
        ";

	$list->title           = "$area Strengths/Weaknesses";
	$list->addURL           = CoreUtils::getURL('09_weakness_edit.php', array('dskey' => $dskey));
	$list->editURL          = CoreUtils::getURL('09_weakness_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_fie_academic";
	$list->deleteKeyField  = "refid";

	$list->addColumn("Strength");
	$list->addColumn("Weakness");

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
