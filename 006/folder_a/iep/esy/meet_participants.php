<?php
    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $set_ini = IDEAFormat::getIniOptions();
    $iepmode = $set_ini['iep_participants_linked_to_iep_year']=='no' ? false : true;

    if (io::get('RefID') == '') {
        $list = new ListClass();

        $list->title = 'ESY Participants';

        $where = ($iepmode) ? 'AND iep_year = ' . $stdIEPYear : '';

        $list->SQL = "SELECT spirefid ,
                             participantname ,
                             participantrole ,
                             participantatttype
                        FROM webset.std_esy_participants
                       WHERE stdrefid = " . $tsRefID . "
                         " . $where . "
                       ORDER BY std_seq_num";

        $list->addColumn('Participant');
        $list->addColumn('Role');
        $list->addColumn('Attendance Type');

        $list->addURL = CoreUtils::getURL('meet_participants.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('meet_participants.php', array('dskey' => $dskey));

        $list->deleteTableName = "webset.std_esy_participants";
        $list->deleteKeyField = "spirefid";

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
                           participantatttype,
                           std_seq_num
                      FROM webset.std_iepparticipants
                     WHERE stdrefid = " . $tsRefID . "
                         " . $where . "
                     ORDER BY std_seq_num, participantname
                ")
                ->addColumn('Participant')
                ->addColumn('Role')
                ->addColumn('Sequence Number')
                ->setDestinationTable('webset.std_esy_participants')
                ->setDestinationTableKeyField('spirefid')
                ->setSourceTable('webset.std_iepparticipants')
                ->setSourceTableKeyField('spirefid')
                ->addPair('stdrefid', $tsRefID, FALSE)
                ->addPair('iep_year', $stdIEPYear, FALSE)
                ->addPair('lastuser', SystemCore::$userUID, FALSE)
                ->addPair('lastupdate', 'NOW()', TRUE)
                ->addPair('participantname', 'participantname', TRUE)
                ->addPair('participantrole', 'participantrole', TRUE)
                ->addPair('std_seq_num', 'std_seq_num', TRUE)
                ->getPopulateButton()
        );

        $list->printList();
    } else {
        $edit = new EditClass('edit1', io::geti('RefID'));

        $edit->title = 'Add/Edit ESY Participants';

        $edit->setSourceTable('webset.std_esy_participants', 'spirefid');

        $edit->addGroup('General Information');
        $edit->addControl('Participant')
            ->name('participantname')
            ->sqlField('participantname')
            ->size(40)
            ->append(FFButton::factory('Find Teacher or Guardian')->onClick('selectUser();'));

        $edit->addControl('Role')
            ->name('participantrole')
            ->sqlField('participantrole')
            ->size(40)
            ->append(FFSelect::factory()
                ->sql(IDEACore::disParam(14) == "Y" ?
                        "
                                SELECT NULL, NULL
                                 UNION ALL
                               (SELECT prddesc, prddesc
                                  FROM webset.disdef_participantrolesdef
                                 WHERE vndrefid = VNDREFID
                                 ORDER BY CASE WHEN substring(prddesc,1,1)='*' THEN 1 ELSE 2 END, prddesc)

                                 " : "

                                 SELECT NULL, NULL
                                  UNION ALL
                                (SELECT prddesc, prddesc
                                   FROM webset.statedef_participantrolesdef
                                  WHERE screfid = " . VNDState::factory()->id . "
                                  ORDER BY CASE WHEN substring(prddesc,1,1)='*' THEN 1 ELSE 2 END, prddesc)
                            ")
                ->onChange('$("#participantrole").val(this.value)')
                ->toHTML());

        $edit->addControl('Attendance Type')
            ->value('In Person')
            ->name('participantatttype')
            ->sqlField('participantatttype')
            ->size(40)
            ->append(FFSelect::factory()
                ->sql("
                                SELECT NULL, NULL
                                 UNION ALL
                               (SELECT patdesc, patdesc
                                  FROM webset.statedef_participantattendancetypes
                                 WHERE screfid = " . VNDState::factory()->id . "
                                 ORDER BY pat_seq, patdesc)
                            ")
                ->onChange('$("#participantatttype").val(this.value)')
                ->toHTML());

        $edit->addControl('Sequence Number', 'integer')->sqlField('std_seq_num')->size(20);

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
        if ($iepmode) $edit->addcontrol("iep year", "hidden")->value($stdIEPYear)->sqlfield('iep_year');

        $edit->firstCellWidth = '15%';

        $edit->finishURL = CoreUtils::getURL('meet_participants.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('meet_participants.php', array('dskey' => $dskey));

        $edit->printEdit();
    }
?>
<script type="text/javascript">
    function selectUser() {
        var wnd = api.window.open('Find Teacher or Guardian', '<?= CoreUtils::getURL('/apps/idea/iep/iepmeeting/iep_participants_users.php', array('dskey' => $dskey)); ?>');
        wnd.resize(950, 600);
        wnd.center();
        wnd.addEventListener('user_selected', onEvent);
        wnd.show();
    }

    function onEvent(e) {
        var name = e.param.name;
        var title = e.param.title;
        $("#participantname").val(name);
        $("#participantrole").val(title);
    }

    function populateUser() {
        var wnd = api.window.open('Populate', '<?= CoreUtils::getURL('meet_populate.php', array('dskey' => $dskey)); ?>');
        wnd.resize(950, 600);
        wnd.center();
        wnd.addEventListener('users_populated', onPopulate);
        wnd.show();
    }

    function onPopulate(e) {
        api.reload();
    }

</script>
