<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Supplementary Aid/Services';

	$list->SQL = "
        SELECT ssmrefid,
               stsdesc || COALESCE('. ' || narrative, ''),
               ssmbegdate,
               COALESCE(ssmtime || '  ','') ||  COALESCE(amt.sadesc || ' ','') ||  COALESCE(freq.sfdesc, ''),
               COALESCE(ssmclasstypenarr, crtdesc),
               COALESCE(umfirstname || ' ' || umlastname, impl_oth),
               nasw
          FROM webset.std_srv_sup std
               INNER JOIN webset.statedef_services_sup state ON std.stsrefid = state.stsrefid
               INNER JOIN webset.def_spedfreq freq ON std.ssmfreq = freq.sfrefid
               INNER JOIN webset.def_spedamt amt ON std.ssmamt = amt.sarefid
               INNER JOIN webset.def_classroomtype class ON std.ssmClassType = class.crtrefid
               LEFT OUTER JOIN public.sys_usermst usr ON std.umrefid = usr.umrefid
         WHERE std.stdrefid=" . $tsRefID . "
         UNION
        SELECT ssmrefid,
               stsdesc || COALESCE('. ' || narrative, ''),
               ssmbegdate,
               COALESCE(ssmtime || '  ','') ||  COALESCE(amt.sadesc || ' ','') ||  COALESCE(freq.sfdesc, ''),
               COALESCE(ssmclasstypenarr, crtdesc),
               COALESCE(umfirstname || ' ' || umlastname, impl_oth),
               nasw
          FROM webset.std_srv_sup std
               INNER JOIN webset.disdef_services_sup dis ON std.dtsrefid = dis.dtsrefid
               INNER JOIN webset.def_spedfreq freq ON std.ssmfreq = freq.sfrefid
               INNER JOIN webset.def_spedamt amt ON std.ssmamt = amt.sarefid
               INNER JOIN webset.def_classroomtype class ON std.ssmclasstype = class.crtrefid
               LEFT OUTER JOIN public.sys_usermst usr ON std.umrefid = usr.umrefid
         WHERE std.stdrefid = " . $tsRefID . "
         ORDER BY 2
    ";

	$list->addColumn('Service');
	$list->addColumn('Beginning Date')->type('date')->dataCallback('clearNAservice');
	$list->addColumn('Frequency')->dataCallback('clearNAservice');
	$list->addColumn('Location')->dataCallback('clearNAservice');
	$list->addColumn('Implementor')->dataCallback('clearNAservice');

	$list->addURL = CoreUtils::getURL('srv_supmst_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('srv_supmst_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_srv_sup';
	$list->deleteKeyField = 'ssmrefid';

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
		->onClick('updateDates("' . CoreUtils::getURL('/apps/idea/iep/services/srv_update_dates.php', array('area' => 'supp', 'dskey' => $dskey)) . '")')
		->leftIcon('calendar.png');

	$list->printList();
	if (IDEACore::disParam(149) == 'Y') {

		$tabs = new UITabs('tabs');

		$tabs->autoHeight(true);

		$tabs->fullFilling(false);

		$tabs->addTab('Notes', CoreUtils::getURL('/apps/idea/iep.mo/services/srv_notes.php', array('area_id' => 2, 'dskey' => $dskey)));

		print $tabs->toHTML();
	}

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
