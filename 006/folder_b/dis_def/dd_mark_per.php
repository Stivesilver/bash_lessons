<?php

    Security::init();

    $list = new ListClass();

    $list->title = 'Building Marking Periods';

    $list->showSearchFields = true;

    $list->SQL = "
        SELECT mp.bmprefid,
               dsy.dsydesc,
               vou.vouname
          FROM webset.sch_markperiod mp
               INNER JOIN public.sys_voumst vou ON mp.vourefid = vou.vourefid
               LEFT OUTER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = mp.dsyrefid
         WHERE vou.vndrefid = VNDREFID
         ORDER BY dsy.dsybgdt DESC, vou.vouname
    ";

    $list->addSearchField('School Year', '', 'list')
        ->sqlField('mp.dsyrefid')
        ->sql("
            SELECT dsyrefid, dsydesc
              FROM webset.disdef_schoolyear
             WHERE vndrefid = VNDREFID
               AND dsyrefid in (SELECT dsyrefid FROM webset.sch_markperiod WHERE vndrefid=VNDREFID)
             ORDER BY dsybgdt DESC
        ");

    $list->addSearchField('School', '', 'list')
        ->sqlField('mp.vourefid')
        ->sql("
            SELECT vourefid, vouname
              FROM sys_voumst
             WHERE vndrefid = VNDREFID
               AND vourefid in (SELECT vourefid FROM webset.sch_markperiod WHERE vndrefid=VNDREFID)
             ORDER by vouname
    ");

    $list->addColumn('School Year');
    $list->addColumn('Building');

    $list->addURL = 'dd_mark_per_add.php';
    $list->editURL = 'dd_mark_per_add.php';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.sch_markperiod')
            ->setKeyField('bmprefid')
            ->applyListClassMode()
    );

    $list->printList();
?>
