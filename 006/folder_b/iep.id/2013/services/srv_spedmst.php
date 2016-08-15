<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$path = '/apps/idea/iep.id/2013/services/by_year_services_list.php';

    $list = new ListClass();

    $list->title = 'Special Education Services';

    $list->SQL = "
        SELECT ssmrefid,
			   order_num,
               COALESCE(stsother, stsdesc),
			   ssmteacherother,
			   impl_oth,
			   COALESCE(ssmclasstypenarr, crtdesc),
			   minutes,
			   sfdesc,
               ssmbegdate,
               ssmenddate,
               nasw
          FROM webset.std_srv_sped std
               INNER JOIN webset.statedef_services_sped state ON std.stsrefid = state.stsrefid
               LEFT JOIN webset.disdef_location class ON std.ssmclasstype = class.crtrefid
			   LEFT JOIN webset.disdef_frequency freq ON std.ssmfreq = freq.sfrefid
         WHERE std.stdrefid = " . $tsRefID . "
		   AND iepyear = ". $stdIEPYear ."
		 ORDER BY order_num, ssmrefid
    ";

    $list->addColumn('Order #');
    $list->addColumn('Service');
	$list->addColumn('Position Responsible')->dataCallback('clearNAservice');
	$list->addColumn('Implementor')->dataCallback('clearNAservice');
    $list->addColumn('Location')->dataCallback('clearNAservice');
    $list->addColumn('Service Time (minutes)')->dataCallback('clearNAservice');
    $list->addColumn('Frequency')->dataCallback('clearNAservice');
    $list->addColumn('Start Date')->type('date')->dataCallback('clearNAservice');
    $list->addColumn('End Date')->type('date')->dataCallback('clearNAservice');

    $list->addURL = CoreUtils::getURL('srv_spedmst_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('srv_spedmst_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_srv_sped';
    $list->deleteKeyField = 'ssmrefid';

	$list->addRecordsResequence(
		'webset.std_srv_sped',
		'order_num'
	);

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
