<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $area = io::get('area');

    $list = new ListClass('list1');

    $list->title = 'IEP Meeting Participants';

    $where = ($area == 'no') ? '' : 'AND iep_year = ' . $stdIEPYear;

    $list->SQL = "
        SELECT spirefid ,
               participantname ,
               participantrole ,
               std_seq_num
          FROM webset.std_iepparticipants
         WHERE stdRefID = " . $tsRefID . "
           AND COALESCE(docarea, 'I') = '" . $area . "'
         ORDER BY CASE WHEN substring(participantrole,1,1)='*' THEN 1 ELSE 2 END, std_seq_num, participantname
    ";

    $list->addColumn('Participant');
    $list->addColumn('Role');
    $list->addColumn('Sequence Number');

    $list->addURL = CoreUtils::getURL('meet_participants_add.php', array('dskey' => $dskey, 'area' => $area));
    $list->editURL = CoreUtils::getURL('meet_participants_add.php', array('dskey' => $dskey, 'area' => $area));

    $list->deleteTableName = 'webset.std_iepparticipants';
    $list->deleteKeyField = 'spirefid';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    if ($area == 'I') {
        $list->title = 'IEP Meeting Participants';
    }

    if ($area == 'A') {
        $list->title = 'Amendment Participants';

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
                ->setDestinationTable('webset.std_iepparticipants')
                ->setDestinationTableKeyField('spirefid')
                ->setSourceTable('webset.std_iepparticipants')
                ->setSourceTableKeyField('spirefid')
                ->addPair('stdrefid', $tsRefID, FALSE)
                ->addPair('docarea', $area, FALSE)
                ->addPair('lastuser', SystemCore::$userUID, FALSE)
                ->addPair('lastupdate', 'NOW()', TRUE)
                ->addPair('participantname', 'participantname', TRUE)
                ->addPair('participantrole', 'participantrole', TRUE)
                ->addPair('std_seq_num', 'std_seq_num', TRUE)
                ->getPopulateButton()
        );
    }

    $list->printList();
?>