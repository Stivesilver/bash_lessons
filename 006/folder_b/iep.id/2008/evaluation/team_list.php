<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $list = new ListClass('list1');

    $list->title = 'Evaluation Team';

    $list->SQL = "
        SELECT refid,
               part_name,
               part_role,
               seq
          FROM webset.es_std_red_part
         WHERE iepyear = " . $stdIEPYear . "
         ORDER BY seq
    ";

    $list->addColumn('Participant');
    $list->addColumn('Role');
    $list->addColumn('Sequence Number');

    $list->addURL = CoreUtils::getURL('team_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('team_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.es_std_red_part';
    $list->deleteKeyField = 'refid';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $list->addButton(
        IDEAPopulateWindow::factory()
            ->addNewItem()
            ->setTitle('IEP Participants')
            ->setSQL("
                    SELECT spirefid ,
                           participantname ,
                           participantrole ,
                           std_seq_num
                      FROM webset.std_iepparticipants
                     WHERE stdRefID = " . $tsRefID . "
                       AND COALESCE(docarea, 'I') = 'I'
                     ORDER BY CASE WHEN substring(participantrole,1,1)='*' THEN 1 ELSE 2 END, std_seq_num, participantname
                ")
            ->addColumn('Participant')
            ->addColumn('Role')
            ->addColumn('Sequence Number')
            ->setDestinationTable('webset.es_std_red_part')
            ->setDestinationTableKeyField('refid')
            ->setSourceTable('webset.std_iepparticipants')
            ->setSourceTableKeyField('spirefid')
            ->addPair('iepyear', $stdIEPYear, FALSE)
            ->addPair('lastuser', SystemCore::$userUID, FALSE)
            ->addPair('lastupdate', 'NOW()', TRUE)
            ->addPair('part_name', 'participantname', TRUE)
            ->addPair('part_role', 'participantrole', TRUE)
            ->addPair('seq', 'std_seq_num', TRUE)
            ->getPopulateButton()
    );

    $list->printList();
?>