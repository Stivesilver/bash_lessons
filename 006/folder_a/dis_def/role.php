<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'IEP Participants Roles';

    $list->showSearchFields = true;

    $list->SQL = "
        SELECT prdrefid,
               prddesc, 
               seq_num
          FROM webset.disdef_participantrolesdef
         WHERE vndrefid = VNDREFID
               ADD_SEARCH 
         ORDER BY seq_num, prddesc
    ";

    $list->addSearchField('Role', 'prddesc');

    $list->addColumn('Role');
    $list->addColumn('Sequence number');

    $list->addURL = 'role_add.php';
    $list->editURL = 'role_add.php';

    $list->deleteTableName = 'webset.disdef_participantrolesdef';
    $list->deleteKeyField = 'prdrefid';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();
?>
