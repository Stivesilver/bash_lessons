<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'District Courses';

    $list->showSearchFields = true;

    $list->SQL = "
        SELECT tsnrefid,
               tsnnum,
        	   tsndesc,
               CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END  as status
	      FROM webset.disdef_tsn
	     WHERE vndrefid = VNDREFID
         ORDER BY tsnnum
    ";

    $list->addSearchField(
        FFIDEAStatus::factory()
            ->sqlField("CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END")
    );

    $list->addColumn('Course #');
    $list->addColumn('Course Description');
    $list->addColumn('Active')->type('switch');

    $list->addURL = 'courses_add.php';
    $list->editURL = 'courses_add.php';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.disdef_tsn')
            ->setKeyField('tsnrefid')
            ->applyListClassMode()
    );

    $list->printList();
?>