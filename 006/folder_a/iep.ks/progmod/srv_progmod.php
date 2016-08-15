<?php

    Security::init();

    $dskey         = io::get('dskey');
    $ds            = DataStorage::factory($dskey);
    $tsRefID       = $ds->safeGet('tsRefID');
    $stdIEPYear    = $ds->safeGet('stdIEPYear');
    $list          = new ListClass();
	$accommodation = new IDEAAccommodation();

    $list->title = 'Program Modifications and Accommodations';

    $list->SQL = "
        SELECT ssmrefid,
               maldesc,
               macdesc,
               stsdesc,
               sfdesc,
               ssmbegdate,
               ssmenddate,
               " . IDEAParts::get('username') . "
           FROM webset.std_srv_progmod std
                LEFT OUTER JOIN webset.statedef_mod_acc_loc loc ON std.malrefid = loc.malrefid
                INNER JOIN webset.statedef_mod_acc acc ON std.stsrefid = acc.stsrefid
                INNER JOIN webset.statedef_mod_acc_cat cat ON acc.macrefid = cat.macrefid
                INNER JOIN webset.def_modfreq frq ON std.ssmfreq = frq.sfrefid
                LEFT OUTER JOIN public.sys_usermst usr ON std.umrefid = usr.umrefid
          WHERE stdrefid = " . $tsRefID . "
          ORDER BY ssmbegdate, macdesc, stsseq, stscode, stsdesc
    ";

    $list->addColumn("Location")->type('group');
    $list->addColumn("Category");
    $list->addColumn("Modification/Accommodation");
    $list->addColumn("Frequency");
    $list->addColumn("Beginning Date")->type('date');
    $list->addColumn("Ending Date")->type('date');
    $list->addColumn("Implementor");

    $list->addURL = CoreUtils::getURL('srv_progmod_add.php',  array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('srv_progmod_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_srv_progmod';
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

	# if exist accommodations without modifications - add alert
	$accommodation->setAttr('tsRefID', $tsRefID);
	$accommodation->getAccommodations();
	$accommodation->sumAccomodations();
	$accommodation->getModificationsByAccommodations();
	$accommodation->sumModifications();
	$accommodation->checkRelationsMod();

	if ($accommodation->getAttr('countNotBinding') > 0) {
		$message = $accommodation->buildNotBindingMessage('stsrefid');
		$list->addObject(
			UIMessage::factory('', UIMessage::NOTE)
				->message($message)
				->textAlign('left')
				->width('100%'),
			ListClassElement::TITLE_BAR_UNDER
		);
	}

    $list->printList();

	io::jsVar('dskey', $dskey);

?>

<script type="text/javascript">

	function addAccommodation(accommodationID) {
		url = api.url('srv_progmod_add.php',
			{'dskey': dskey, 'RefID': 0, 'accommodationID': accommodationID});
		win = api.window.open(' ', url)
			.addEventListener(
			WindowEvent.CLOSE,
			function(e) {
				ListClass.get().reloadPage();
			}
		);
	}

</script>
