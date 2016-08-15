
<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $list = new ListClass();

    $list->title = 'Form D - Part 3: State Accommodations for WIDA-ACCESS';

    $list->SQL = "SELECT refid,
                         catname,
                         accname,
                         plpgsql_recs_to_str('SELECT CAST(domain as varchar) ||
                                                     CAST(CASE WHEN drefid in (' || COALESCE(invaliddomains,'0') || ') THEN ''(See Note 1)''  ELSE '''' END as varchar) AS column
                                              FROM webset.statedef_aa_wida_domain
                                             WHERE drefid in (' || COALESCE(alloweddomains,'0') || ')
                                               AND (enddate IS NULL or now()< enddate)
                                               AND drefid in (' || COALESCE(domains,'0')  || ')
                                             ORDER BY seqnum, domain', ', ')
                    FROM webset.std_form_d_wida std
                         INNER JOIN webset.statedef_aa_wida_acc acc ON std.accrefid = acc.accrefid
                         INNER JOIN webset.statedef_aa_wida_cat cat ON catrefid = acccat
                   WHERE stdrefid = " . $tsRefID . "
                     AND syrefid = " . $stdIEPYear . "
                   ORDER BY cat.seqnum, cat.catname, acc.seqnum, acc.accname";

    $list->addColumn('Category')->type('group');
    $list->addColumn('Accommodations');
    $list->addColumn('Assessment Domains');

    $list->deleteTableName = 'webset.std_form_d_wida';
    $list->deleteKeyField = 'refid';

    $list->addURL = CoreUtils::getURL('part3add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('part3add.php', array('dskey' => $dskey));

    $list->getButton(ListClassButton::ADD_NEW)
        ->disabled(db::execSQL("
                        SELECT 1
                          FROM webset.statedef_aa_wida_acc acc
                               INNER JOIN webset.statedef_aa_wida_cat cat ON catrefid = acccat
                         WHERE (cat.enddate IS NULL or now()< cat.enddate)
                           AND (acc.enddate IS NULL or now()< acc.enddate)
                           AND accrefid NOT in (SELECT std.accrefid
                                                  FROM webset.std_form_d_wida std
                                                 WHERE stdrefid=" . $tsRefID . "
                                                   AND syrefid=" . $stdIEPYear . ")
                       ")->getOne() != '1');

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );


    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $list->printList();

    include("notes3.php");
?>
