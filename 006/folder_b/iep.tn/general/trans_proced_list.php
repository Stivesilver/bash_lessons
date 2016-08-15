<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area_id = IDEAAppArea::TN_IFSP_PLEP;

	$list = new ListClass();

	$list->title = 'Transitioning Procedures';

	$list->SQL = "
		SELECT refid,
			   txt01,
			   txt02,
			   txt03,
			   dat01
		  FROM webset.std_general
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " .$stdIEPYear  . "
	       AND area_id = " . IDEAAppArea::TN_IFSP_TRANSITION_FORMC . "
		 ORDER BY txt01
	";

	$list->addColumn('Planned Transitioning Procedures')
		->sqlField('txt01');

	$list->addColumn('Implementor')
		->sqlField('txt02');

	$list->addColumn('Timeframe')
		->sqlField('txt03');

	$list->addColumn('Date Completed')
		->type('date')
		->sqlField('dat01');

	$list->addURL = CoreUtils::getURL('trans_proced_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('trans_proced_edit.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_general';
	$list->deleteKeyField = 'refid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		FFIDEAHelpButton::factory()
			->setHTMLByConstruction(IDEAAppArea::TN_IFSP_TRANSITION_FORMC)
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();
?>

