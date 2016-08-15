<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Supports For School Personnel';

	$list->SQL = "
        SELECT sspmrefid,
               COALESCE (sspnarrative, sspdesc),
               sspbegdate,
               sspenddate,
               nasw
          FROM webset.std_srv_supppersonnel std
               INNER JOIN webset.statedef_services_supppersonnel state ON std.ssprefid = state.ssprefid
         WHERE stdrefid = " . $tsRefID . "
         ORDER BY 2
    ";

	$list->addColumn('Supports For School Personnel');
	$list->addColumn('Beginning Date')->type('date')->dataCallback('clearNAservice');
	$list->addColumn('Ending Date')->type('date')->dataCallback('clearNAservice');

	$list->addURL = CoreUtils::getURL('srv_supp_pers_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('srv_supp_pers_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_srv_supppersonnel';
	$list->deleteKeyField = 'sspmrefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addButton('Update Dates')
		->onClick('updateDates("' . CoreUtils::getURL('/apps/idea/iep/services/srv_update_dates.php', array('area' => 'pers', 'dskey' => $dskey)) . '")')
		->leftIcon('calendar.png');

	$list->printList();

	function clearNAservice($data, $col) {
		if ($data['nasw'] == 'Y') {
			return '';
		} else {
			return $data[$col];
		}
	}

?>
<script type="text/javascript">
	function updateDates(url) {
		var wnd = api.window.open('Update Services Dates', url);
		wnd.resize(600, 400);
		wnd.center();
		wnd.addEventListener('dates_updated', onEvent);
		wnd.show();
	}

	function onEvent() {
		api.reload();
	}

</script>
