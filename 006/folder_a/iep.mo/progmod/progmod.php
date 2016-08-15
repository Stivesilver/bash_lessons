<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    $list = new ListClass();

    $list->title = 'Program Modifications and Accommodations';

    $list->SQL = "
        SELECT stsrefid,
               macdesc,
               stsdesc || ' ' || plpgsql_recs_to_str('
                                     SELECT val AS column
                                       FROM webset.std_progmod
                                      WHERE stdrefid = " . $tsRefID . "
                                        AND typeofval = ''oth''
                                        AND stsrefid = ' || stsrefid, ''),

               plpgsql_recs_to_str('
                   SELECT maldesc AS column
                     FROM webset.std_progmod
                          INNER JOIN webset.statedef_mod_acc_loc ON val_id::int = malrefid
                    WHERE stdrefid = " . $tsRefID . "
                      AND typeofval = ''loc''
                      AND stsrefid = ' || stsrefid || '
                    ORDER BY seq_num, maldesc', '<br/>'),

               plpgsql_recs_to_str('
                   SELECT esfumdesc AS column
                     FROM webset.std_progmod
                          INNER JOIN webset.statedef_esy_serv_freq_unit_of_measur ON val_id::int = esfumrefid
                    WHERE stdrefid = " . $tsRefID . "
                      AND typeofval = ''frq''
                      AND stsrefid = ' || stsrefid || '
                    ORDER BY CASE esfumdesc
                                 WHEN ''Daily''   THEN 1
                                 WHEN ''Weekly''  THEN 2
                                 WHEN ''Monthly'' THEN 3
                                 WHEN ''Other''   THEN 4
                             END', '<br/>'),

               plpgsql_recs_to_str('
                   SELECT val AS column
                     FROM webset.std_progmod
                    WHERE stdrefid = " . $tsRefID . "
                      AND typeofval = ''beg''
                      AND stsrefid = ' || stsrefid, ''),

               plpgsql_recs_to_str('
                   SELECT val AS column
                     FROM webset.std_progmod
                    WHERE stdrefid = " . $tsRefID . "
                      AND typeofval = ''end''
                      AND stsrefid = ' || stsrefid, '')
          FROM webset.statedef_mod_acc acc
               INNER JOIN webset.statedef_mod_acc_cat cat ON cat.macrefid = acc.macrefid
         WHERE EXISTS (SELECT 1
                         FROM webset.std_progmod std
                        WHERE stdrefid = " . $tsRefID . "
                          AND std.stsrefid = acc.stsrefid)
         ORDER BY seq_num, stsseq, stsdesc
    ";

    $list->addColumn('Category')->type('group');
    $list->addColumn('Modifications/Accommodations');
    $list->addColumn('Location');
    $list->addColumn('Frequency');
    $list->addColumn('Begin Date');
    $list->addColumn('End Date');

    $list->addURL = CoreUtils::getURL('progmod_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('progmod_add.php', array('dskey' => $dskey));

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_progmod')
            ->setKeyField('stsrefid')
            ->applyListClassMode()
    );

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $list->addRecordsProcess('Delete')
        ->message('Do you really want to delete selected Modifications/Accommodations?')
        ->url(CoreUtils::getURL('progmod_delete.ajax.php', array('dskey' => $dskey)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false);

    $list->printList();
?>

