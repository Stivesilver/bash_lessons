<?php
    Security::init();

    $list = new ListClass();

    $list->title = 'ESY services';
    
    $list->SQL = "
        SELECT desdrefid, 
               desddesc, 
               desdactivesw
          FROM webset.disdef_esy_services
         WHERE vndrefid = VNDREFID
         ORDER BY TRIM(desddesc)
    ";

    $list->addColumn('ESY service');
        
    $list->addColumn('Active Record');

    $list->addURL  = 'esy_serv_mst_add.php';
    $list->editURL = 'esy_serv_mst_add.php';

    $list->deleteTableName = 'webset.disdef_esy_services';
    $list->deleteKeyField  = 'desdrefid';
    
    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();

?>
