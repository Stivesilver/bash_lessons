<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'Progress Reports Extent';

    $list->multipleEdit = false;
    $list->showSearchFields = true;

    $list->SQL = "
        SELECT eprefid,
               epsdesc,
               epldesc,
               ephelpmsg,
               epseq,
               CASE WHEN NOW() > recdeactivationdt  THEN 'In-Active' ELSE 'Active' END  as status                         
          FROM webset.disdef_progressrepext
         WHERE vndrefid = VNDREFID
         ORDER BY epseq, eprefid
    ";

    $list->addSearchField('Status', '', 'list')
        ->value('1')
        ->sqlField('(CASE recdeactivationdt<now() WHEN true THEN 2 ELSE 1 END)')
        ->data(array(1 => 'Active', 2 => 'Inactive'));

    $list->addColumn('Short value');
    $list->addColumn('Complete Description');
    $list->addColumn('User Help Message');
    $list->addColumn('Sequence');
    $list->addColumn('Status');

    $list->addURL = 'ds_progress_ext_add.php';
    $list->editURL = 'ds_progress_ext_add.php';

    $list->hideCheckBoxes = FALSE;
    
    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.disdef_progressrepext')
            ->setKeyField('eprefid')
            ->applyListClassMode()
    );

    $list->printList();
?>
