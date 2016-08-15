<?php

	Security::init();

	$dskey = io::get('dskey');
	$area = io::get('area');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Summary/Additional Comments/Recommendations';

	$list->SQL = "
		SELECT siairefid,
			   siaitext
		  FROM webset.std_additionalinfo
		 WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
		   AND COALESCE(docarea, 'A') = '" . $area . "'
		 ORDER BY siairefid
	";

	$list->addColumn('Student IEP Additional Information');

	$list->addURL = CoreUtils::getURL('summary_add.php', array('dskey' => $dskey, 'area' => $area));
	$list->editURL = CoreUtils::getURL('summary_add.php', array('dskey' => $dskey, 'area' => $area));

	$list->deleteTableName = 'webset.std_additionalinfo';
	$list->deleteKeyField = 'siairefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();
?>