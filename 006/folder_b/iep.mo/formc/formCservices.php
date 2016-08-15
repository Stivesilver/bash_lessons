<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $list = new ListClass();

    $list->title = 'Transition Services';

    $list->SQL = "
       SELECT refid,
              CASE tsna = 'Y' WHEN TRUE THEN tadesc || ' (N/A)' ELSE tadesc END,
              postgoals,
              tsdesc
         FROM webset.std_form_c_serv
              LEFT JOIN webset.statedef_transarea ON webset.std_form_c_serv.tarefid = webset.statedef_transarea.tarefid
        WHERE stdrefid = " . $tsRefID . "
          AND syrefid = " . $stdIEPYear . "
        ORDER BY seqnum, tadesc
    ";

    $list->addColumn("Area");
    $list->addColumn("Postsecondary Goal(S)");
    $list->addColumn("Description of Service");

    $list->deleteTableName = "webset.std_form_c_serv";
    $list->deleteKeyField = "refid";

    $list->addURL = CoreUtils::getURL('formCadd.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));
    $list->editURL = CoreUtils::getURL('formCadd.php', array('dskey' => $dskey, 'spconsid' => io::geti('spconsid')));

    $list->getButton(ListClassButton::ADD_NEW)
        ->disabled(db::execSQL("
                       SELECT count(1)
                         FROM webset.statedef_transarea state
                        WHERE (enddate IS NULL or now()< enddate)
                          AND NOT EXISTS (SELECT 1
                                            FROM webset.std_form_c_serv std
                                           WHERE stdrefid = " . $tsRefID . "
                                             AND syrefid = " . $stdIEPYear . "
                                             AND state.tarefid = std.tarefid)
                       ")->getOne() == '0');

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
?>