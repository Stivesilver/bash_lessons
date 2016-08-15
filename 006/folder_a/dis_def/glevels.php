<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'Grade Levels';

    $list->SQL = "
        SELECT gl_refid,
               gl_code,
               gl_desc,
               gl_numeric_value,
               gl_refid
          FROM c_manager.def_grade_levels
         WHERE vndrefid = VNDREFID
         ORDER BY gl_numeric_value
    ";

    $list->addColumn('Grade Code');
    $list->addColumn('Description');
    $list->addColumn('Numeric Value');
    $list->addColumn('Lumen ID');

    $list->deleteTableName = 'c_manager.def_grade_levels';
    $list->deleteKeyField = 'gl_refid';

    $list->editURL = 'glevel_add.php';
    $list->addURL = 'glevel_add.php';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('c_manager.def_grade_levels')
            ->setKeyField('gl_refid')
            ->applyListClassMode()
    );

    $list->printList();
?>
