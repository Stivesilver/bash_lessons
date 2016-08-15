<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'District School Year';

    $list->SQL = "
        SELECT dsyrefid,
               dsydesc,
               dsybgdt,
               dsyendt
          FROM webset.disdef_schoolyear
         WHERE vndrefid = VNDREFID
         ORDER BY dsybgdt DESC
    ";

    $list->addColumn('School Year Description');
    $list->addColumn('Start of school year', '', 'date');
    $list->addColumn('End of school year', '', 'date');

    $list->addURL = 'dd_sy_add.php';
    $list->editURL = 'dd_sy_add.php';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.disdef_schoolyear')
            ->setKeyField('dsyrefid')
            ->applyListClassMode()
    );
    

    $list->printList();
?>
