<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory(io::get('dskey'))->safeGet('tsRefID');

    $list = new ListClass();

    $list->title = 'Student Disability';

    $list->SQL = "
        SELECT sdrefid,
               dccode,
               dcdesc,
               validvalue
          FROM webset.std_disabilitymst std
               INNER JOIN webset.statedef_disablingcondition state ON std.dcrefid = state.dcrefid
               LEFT OUTER JOIN webset.glb_validvalues ON CAST(sdtype as varchar) = validvalueid AND  valuename = 'IDDisabilityType'
         WHERE std.stdrefid = " . $tsRefID . "
         ORDER BY std.sdtype, state.dcCode
    ";

    $list->addColumn('ID')->width('10%');
    $list->addColumn('Disability Category')->width('65%');
    $list->addColumn('Type')->width('25%');

    $list->addURL = CoreUtils::getURL('cd_dis_cat_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('cd_dis_cat_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_disabilitymst';
    $list->deleteKeyField = 'sdrefid';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();
?>