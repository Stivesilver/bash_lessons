<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $student = new IDEAStudent($tsRefID);

    if ($student->get('ecflag') == 'Y') {
        $title = 'Educational Environment';
    } else {
        $title = 'Placement Category';
    }
    $ds->set('Placement Title', $title);

    $list = new ListClass();

    $list->title = $title;

    $list->SQL = "
        SELECT std.pcrefid,
               state.spccode,
               state.spcdesc
          FROM webset.std_placementcode std
               INNER JOIN webset.statedef_placementcategorycode state ON std.spcrefid=state.spcrefid
         WHERE std.stdrefid = " . $tsRefID . "
         ORDER BY spccode
    ";

    $list->addColumn('Code')->sqlField('spccode');
    $list->addColumn($title)->sqlField('spcdesc');

    $list->addURL = CoreUtils::getURL('cd_place_cat_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('cd_place_cat_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_placementcode';
    $list->deleteKeyField = 'pcrefid';


    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $list->getButton(ListClassButton::ADD_NEW)
        ->disabled(db::execSQL("
                        SELECT 1 
                          FROM webset.std_placementcode
                         WHERE stdrefid=" . $tsRefID)->getOne() == '1');

    $list->printList();
?>
