<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$editUrl    = CoreUtils::getURL('02_weaknesses_edit.php', array('dskey' => $dskey));
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$area       = 3;
	$list       = new ListClass();

	$list->showSearchFields = true;
	$list->addURL           = $editUrl;
	$list->editURL          = $editUrl;
	$list->deleteTableName  = "webset_tx.std_fie_academic";
	$list->deleteKeyField   = "refid";
	$list->SQL              = "
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

	$list->title = "$area Strengths/Weaknesses";

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


?>