<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$path = '/apps/idea/iep.id/2013/services/by_year_suplementary_list.php';

	$list = new ListClass();

	$list->title = 'Supplementary Aids and Services';

	$list->SQL = "
        SELECT ssmrefid,
               COALESCE(narrative, stsdesc),
			   ssmteacherother,
               ssmbegdate,
               ssmenddate,
               nasw
          FROM webset.std_srv_sup std
               INNER JOIN webset.statedef_services_sup state ON std.stsrefid = state.stsrefid
               LEFT OUTER JOIN public.sys_usermst usr ON std.umrefid = usr.umrefid
         WHERE std.stdrefid=" . $tsRefID . "
		   AND iepyear = ". $stdIEPYear ."
         ORDER BY 2
    ";

	$list->addColumn('Service');
	$list->addColumn('Position Responsible')->dataCallback('clearNAservice');
	$list->addColumn('Start Date')->type('date')->dataCallback('clearNAservice');
	$list->addColumn('Duration')->type('date')->dataCallback('clearNAservice');

	$list->addURL = CoreUtils::getURL('srv_supmst_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('srv_supmst_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_srv_sup';
	$list->deleteKeyField = 'ssmrefid';

	$button = new IDEAPopulateIEPYear($dskey, null, $path);
	$listButton = $button->getPopulateButton();
	$list->addButton($listButton);

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

	function clearNAservice($data, $col) {
		if ($data['nasw'] == 'Y') {
			return '';
		} else {
			return $data[$col];
		}
	}

?>
