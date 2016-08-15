<?php

    Security::init();

    $list = new listClass();
    $list->title = 'Teachers Activity';
    $list->showSearchFields = true;
    $list->printable = true;

    $list->SQL = "
        SELECT suajrefid,
               umlastname || ' ' || umfirstname,
               stdlnm || ' ' || stdfnm,
               t02.mdmenutext,
               suajdt
          FROM webset.sped_useraccessjournal t1
               INNER JOIN sys_usermst t2 ON t2.umrefid = t1.suajuserid
               INNER JOIN webset.sys_teacherstudentassignment t4 ON t4.tsrefid=t1.tsrefid
               INNER JOIN webset.dmg_studentmst t5 ON t4.stdrefid=t5.stdrefid
               INNER JOIN webset.disdef_spedmenu AS ms ON t2.vndrefid = ms.vndrefid
               INNER JOIN webset.sped_menu AS t01 ON t01.mrefid = t1.mrefid
               INNER JOIN webset.sped_menudef AS t02 ON t02.mdrefid = t01.mdrefid
         WHERE t2.vndrefid = VNDREFID
         ORDER BY t1.suajdt DESC
    ";

    $list->addSearchField(FFStudentName::factory());
    $list->addSearchField(FFUserName::factory());

    $list->addSearchField("App Name:", "lower(mdmenutext)  like '%' || lower(ADD_VALUE)|| '%'");
    $list->addSearchField('Date Range', 'suajdt', 'daterange')
        ->name('msd_date')
        ->value(date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))));

    $list->addColumn("User Name");
    $list->addColumn("Student Name");
    $list->addColumn("Sp Ed Application");
    $list->addColumn("Access Date")->type('datetime');

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.sped_useraccessjournal')
            ->setKeyField('suajrefid')
            ->applyListClassMode()
    );

    $list->printList();
?>