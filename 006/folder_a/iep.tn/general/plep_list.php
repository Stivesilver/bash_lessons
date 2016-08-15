<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area_id = IDEAAppArea::TN_IFSP_PLEP;

	$list = new ListClass();

	$list->title = 'Present Levels of Development';

	$list->SQL = "
		SELECT pglp.pglprefid,
			   tsn.tsndesc,
			   pglp.strengths,
			   pglp.concerns,
			   pglp.pglpnarrative,
			   pglp.pgdate
		  FROM webset.std_in_pglp AS pglp
		  	   LEFT OUTER JOIN webset.disdef_tsn AS tsn ON tsn.tsnrefid = pglp.tsnrefid
		 WHERE pglp.stdrefid = " . $tsRefID . "
		   AND pglp.iepyear = " . $stdIEPYear . "
		 ORDER BY pglp.pglpseq, tsn.tsnnum
	";

	$list->addColumn('Area')
		->sqlField('tsndesc');

	$list->addColumn('Strength')
		->sqlField('strengths');

	$list->addColumn('Needs')
		->sqlField('concerns');

	$list->addColumn('By')
		->sqlField('pglpnarrative');

	$list->addColumn('Date')
		->type('date')
		->sqlField('pgdate');

	$list->addURL = CoreUtils::getURL('plep_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('plep_edit.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_in_pglp';
	$list->deleteKeyField = 'pglprefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		FFIDEAHelpButton::factory()
			->setHTMLByConstruction($area_id)
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();
?>
