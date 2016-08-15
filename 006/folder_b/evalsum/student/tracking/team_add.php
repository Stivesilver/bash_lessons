<?php
    Security::init();

    $dskey = io::get('dskey');
    $eprefid = io::geti('eprefid');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');    
    
    //GET ATTENDANCE TYPE DROPDOWN
    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add Evaluation Team Member';

    $edit->setSourceTable('webset.es_std_evalproc_part', 'spirefid');

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
            ->sql(IDEACore::disParam(14) == 'Y' ?
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

    $edit->addControl('Order #', 'integer')
        ->sqlField('std_seq_num')
        ->value((int) db::execSQL("
	                    SELECT count(1)
	                      FROM webset.es_std_evalproc_part
	                     WHERE evalproc_id = " . $eprefid . "
	                ")->getOne() + 1
        )
        ->size(20);

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Process ID', 'hidden')->value($eprefid)->sqlField('evalproc_id');

    $edit->firstCellWidth = '15%';

    $edit->finishURL = CoreUtils::getURL('team_list.php', array('dskey' => $dskey, 'eprefid' => $eprefid));
    $edit->cancelURL = CoreUtils::getURL('team_list.php', array('dskey' => $dskey, 'eprefid' => $eprefid));

    $edit->printEdit();
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

</script>