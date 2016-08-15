<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $smode = io::get('smode');

    $list = new ListClass();

    $list->title = ($smode == 'A') ? 'Amended Services' : 'Services';

    $list->multipleEdit = false;

    $list->SQL = "
    	SELECT ssmrefid,
               typedesc,
               stsdesc || COALESCE(' (' || comments || ') ', ''),
               ksssstatecode,
               std.begdate,
               std.enddate,
               provider,
               sfdesc
          FROM webset.std_srv_all std
               INNER JOIN webset.statedef_services_type ON webset.statedef_services_type.trefid = std.srv_type
               INNER JOIN webset.statedef_services_all ON webset.statedef_services_all.stsrefid = std.srvrefid
               LEFT  JOIN webset.statedef_services_set ON webset.statedef_services_set.ksssstatecode = std.setting_whole
               LEFT  JOIN webset.disdef_frequency ON sfrefid = freq_id
         WHERE std.stdRefID = " . $tsRefID . "
           AND iep_year = " . $stdIEPYear . "
           AND COALESCE(smode, '') = '" . $smode . "'
         ORDER BY typedesc, stsdesc, begdate
    ";

    $list->addColumn("Type");
    $list->addColumn("Service");
    $list->addColumn("Setting");
    $list->addColumn("Beg Date")->type('date');
    $list->addColumn("Ending Date")->type('date');
    $list->addColumn("Provider");
    $list->addColumn("Frequency");

    $list->deleteTableName = "webset.std_srv_all";
    $list->deleteKeyField = "ssmrefid";

    $list->addURL = CoreUtils::getURL('srv_add.php', array('dskey' => $dskey, 'smode' => $smode));
    $list->editURL = CoreUtils::getURL('srv_add.php', array('dskey' => $dskey, 'smode' => $smode));

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