<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $area_id = 125;

    $list = new ListClass('list1');

    $list->title = 'Evaluation Team (SLD)';

    $list->SQL = "
        SELECT refid,
               txt01,
               txt02,
               int01
          FROM webset.std_general
         WHERE stdrefid = " . $tsRefID . "
           AND area_id = " . $area_id . "
         ORDER BY int01
    ";

    $list->addColumn('Name');
    $list->addColumn('Role');
    $list->addColumn('Sequence');

    $list->addURL = CoreUtils::getURL('team_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('team_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_general';
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
            ->setDestinationTable('webset.std_general')
            ->setDestinationTableKeyField('refid')
            ->setSourceTable('webset.std_iepparticipants')
            ->setSourceTableKeyField('spirefid')
            ->addPair('stdrefid', $tsRefID, FALSE)
            ->addPair('area_id', $area_id, FALSE)
            ->addPair('lastuser', SystemCore::$userUID, FALSE)
            ->addPair('lastupdate', 'NOW()', TRUE)
            ->addPair('txt01', 'participantname', TRUE)
            ->addPair('txt02', 'participantrole', TRUE)
            ->addPair('int01', 'std_seq_num', TRUE)
            ->getPopulateButton()
    );

    $list->printList();
?>