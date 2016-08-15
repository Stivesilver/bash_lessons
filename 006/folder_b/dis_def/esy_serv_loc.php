<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'ESY Services Locations';

    $list->SQL = "
        SELECT desldrefid, 
               deslddesc, 
               desldactivesw
          FROM webset.disdef_esy_serv_loc
         WHERE vndrefid = VNDREFID
    ";

    $list->addColumn('ESY Services Location');
    $list->addColumn('Active Record');

    $list->addURL = 'esy_serv_loc_add.php';
    $list->editURL = 'esy_serv_loc_add.php';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.disdef_esy_serv_loc')
            ->setKeyField('desldrefid')
            ->applyListClassMode()
    );

    $list->printList();
?>
