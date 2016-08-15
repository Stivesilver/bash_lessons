<?php

	Security::init();

	$dskey = io::get('dskey');
	$mode = io::get('mode');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Previous Efforts/Options Considered';

	$list->SQL = "
		SELECT t1.refid,
			   CASE modesw WHEN 'M' THEN 'Modifications/accommodations for' ELSE 'Efforts' END,
			   edesc || CASE WHEN edesc like 'Other%' THEN COALESCE(' ' || other, '') ELSE '' END,
			   CASE mark WHEN 'S' THEN 'Y' WHEN 'U' THEN 'N' ELSE '' END
		  FROM webset_tx.def_lre_efforts AS t0
			   INNER JOIN webset_tx.std_lre_efforts AS t1 ON t1.erefid = t0.refid
		 WHERE stdrefid = " . $tsRefID . "
		   AND iep_year = " . $stdIEPYear . "
		   AND smode = '" . $mode . "'
		 ORDER BY t0.seqnum, edesc
    ";

	$list->addColumn('')->type('group');
	$list->addColumn('Effort/Modification');
	$list->addColumn('Successful Rate')->type('switch');

	$list->addURL = CoreUtils::getURL('efforts_add.php', array('dskey' => $dskey, 'mode' => $mode));
	$list->editURL = CoreUtils::getURL('efforts_add.php', array('dskey' => $dskey, 'mode' => $mode));

	$list->deleteTableName = 'webset_tx.std_lre_efforts';
	$list->deleteKeyField = 'refid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$notAnswered = db::execSQL("
		SELECT 1
		  FROM webset_tx.def_lre_efforts
		 WHERE NOT EXISTS (SELECT 1
						     FROM webset_tx.std_lre_efforts
						    WHERE stdrefid = " . $tsRefID . "
							  AND iep_year = " . $stdIEPYear . "
							  AND smode = '" . $mode . "'
							  AND erefid = webset_tx.def_lre_efforts.refid)
	")->getOne();

	$list->getButton(ListClassButton::ADD_NEW)
		->disabled($notAnswered == 0);

	$list->printList();
?>