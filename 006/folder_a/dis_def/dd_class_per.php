<?php

    Security::init();

    $list = new ListClass();

    $list->SQL = "
        SELECT bcprefid, 
               vouname, 
               bcpdesc, 
               bcpseqnumber
          FROM webset.sch_classperiods dis
               INNER JOIN public.sys_voumst vou ON dis.vourefid = vou.vourefid
         WHERE vou.vndrefid = VNDREFID
         ORDER BY vouname, bcpseqnumber, bcpdesc
    ";

    $list->title = 'Building Class Period';

    $list->addColumn('Building');
    $list->addColumn('Period');
    $list->addColumn('Sequence');

    $list->addURL = 'dd_class_per_add.php';
    $list->editURL = 'dd_class_per_add.php';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.sch_classperiods')
            ->setKeyField('bcprefid')
            ->applyListClassMode()
    );

    $list->printList();
?>
