<?php

    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $list       = new ListClass();

    $list->addURL          = CoreUtils::getURL('srv_assessments_edit.php',  array('dskey' => $dskey));
    $list->editURL         = CoreUtils::getURL('srv_assessments_edit.php', array('dskey' => $dskey));
    $list->title           = "Testing Accommodations";
    $list->deleteTableName = "webset.std_assess_state";
    $list->deleteKeyField  = "sswarefid";
    $list->SQL             = "
        SELECT sswarefid,
               aaadesc,
               swadesc || COALESCE (' ' || sswanarr, ''),
               plpgsql_recs_to_str ('SELECT cast(validvalueid as varchar)  AS column
                                       FROM webset.glb_validvalues
                                      WHERE refid in (' || COALESCE(assessmode,'0') || ')', ', ') as assessmode,
               TRIM(plpgsql_recs_to_str ('SELECT pmdesc AS column
                                            FROM webset.disdef_progmod acc
                                            LEFT OUTER JOIN webset.disdef_progmodcat cat ON cat.catrefid = acc.catrefid
                                           WHERE refid IN (' || COALESCE(accomm_ids,'') || '0)
                                           ORDER BY cat.seqnum, categor, acc.seqnum, pmdesc
                                           LIMIT 40', '<br>')  ||
               COALESCE('<br>' || accomod, ''), '<br>')
          FROM webset.std_assess_state std
               INNER JOIN webset.statedef_assess_links  links ON lrefid = swarefid
               INNER JOIN webset.statedef_assess_state  ass ON ass.swarefid = assessment_id
               INNER JOIN webset.statedef_assess_acc    sbj ON sbj.aaarefid = subject_id
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
         ORDER BY aaadesc, swaseq, swadesc
       ";

    $list->addColumn('Language Arts', null, 'group');

    $list->addColumn('Assessment');

    $list->addColumn('Mode');

    $list->addColumn('Accommodations');

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();

?>
