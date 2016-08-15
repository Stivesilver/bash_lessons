<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'ESY Dates';

    $list->SQL = "
        SELECT refid,
               begdate,
               enddate
          FROM webset.disdef_esy_dates
         WHERE vndrefid = VNDREFID
    ";

    $list->addColumn('Begin Date', '', 'date');
    $list->addColumn('End Date', '', 'date');

    $list->addURL = 'esy_dates_add.php';
    $list->editURL = 'esy_dates_add.php';

    $list->deleteTableName = 'webset.disdef_esy_dates';
    $list->deleteKeyField = 'refid';

    $list->getButton(ListClassButton::ADD_NEW)
        ->disabled(
            db::execSQL("
                SELECT 1 
                  FROM webset.disdef_esy_dates
                 WHERE vndrefid = VNDREFID
            "
            )->getOne() == '1'
    );

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();
?>
