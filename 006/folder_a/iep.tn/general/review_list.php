<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();
	$list->title = 'Review/Changes';

	$list->SQL = "
		SELECT std.refid,
			   gval.validvalue,
			   std.txt02,
			   std.dat01
		  FROM webset.std_general AS std
		  	   LEFT JOIN webset.glb_validvalues AS gval ON(std.int01 = gval.refid)
		 WHERE std.stdrefid = $tsRefID
		   AND std.area_id = " . IDEAAppArea::TN_IFSP_OUTCOME_ACTION . "
		 ORDER BY int01
	";

	$list->addColumn('Review Status Key')
		->sqlField('validvalue');

	$list->addColumn('Comment')
		->sqlField('txt02');

	$list->addColumn('Date')
		->sqlField('dat01')
		->type('date');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_general')
			->setKeyField('refid')
			->applyListClassMode()
	);

	$list->addButton(
		FFIDEAHelpButton::factory()
			->setHTMLByConstruction(IDEAAppArea::TN_IFSP_OUTCOME_ACTION)
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addURL = CoreUtils::getURL('./review_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('./review_edit.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_general';
	$list->deleteKeyField = 'refid';

	$list->printList();
?>
