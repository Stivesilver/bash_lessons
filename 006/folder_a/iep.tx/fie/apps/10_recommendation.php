<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT refid,
		  CASE WHEN LOWER(r_name) LIKE '%other%' THEN r_name || ' ' || other ELSE r_name END
		  FROM webset_tx.std_fie_recommendation
		 INNER JOIN webset_tx.def_fie_recommendation USING (r_refid)
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
               ADD_SEARCH
         ORDER BY r_name
         ";

	$list->title           = "Recommendations";
	$list->addURL          = CoreUtils::getURL('10_recommendation_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('10_recommendation_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_fie_recommendation";
	$list->deleteKeyField  = "refid";

	$list->addColumn("Description")->width('100%');

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