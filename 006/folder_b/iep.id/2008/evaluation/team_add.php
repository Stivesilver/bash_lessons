<?php
    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    //GET ATTENDANCE TYPE DROPDOWN
    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add Evaluation Team';

    $edit->setSourceTable('webset.es_std_red_part', 'refid');

    $edit->addGroup('General Information');
    $edit->addControl('Participant')
        ->name('part_name')
        ->sqlField('part_name')
        ->size(40)
        ->append(FFButton::factory('Find Teacher or Guardian')->onClick('selectUser();'));

    $edit->addControl('Role')
        ->name('part_role')
        ->sqlField('part_role')
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
            ->onChange('$("#part_role").val(this.value)')
            ->toHTML());


    $edit->addControl('Sequence Number', 'integer')
        ->sqlField('seq')
        ->value((int) db::execSQL("
	                    SELECT max(seq)
	                      FROM webset.es_std_red_part
	                     WHERE iepyear = " . $stdIEPYear . "
	                ")->getOne() + 10
        )
        ->size(20);

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');


    $edit->firstCellWidth = '15%';

    $edit->finishURL = CoreUtils::getURL('team_list.php', array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL('team_list.php', array('dskey' => $dskey));

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
        $("#part_name").val(name);
        $("#part_role").val(title);
    }

</script>