<?php
    Security::init();

    $dskey = io::get('dskey');
	$RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $area = io::get('area');

    //GET ATTENDANCE TYPE DROPDOWN
    $edit = new EditClass('edit1', $RefID);

    $edit->title = 'Add IEP Meeting Participants';

    $edit->setSourceTable('webset.std_iepparticipants', 'spirefid');

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

    $edit->addControl('Sequence Number', 'integer')
        ->sqlField('std_seq_num')
        ->value((int) db::execSQL("
	                    SELECT max(std_seq_num)
	                      FROM webset.std_iepparticipants
	                     WHERE stdrefid = " . $tsRefID . "
                           AND COALESCE(docarea, 'I') = '" . $area . "'
	                ")->getOne() + 10
        )
        ->size(20);

	if (IDEACore::disParam(2) == 'Y') {
		$signature = $edit->addControl(FFSignature::factory())
			->name('signature_file')
			->onChange('setSignature(this.value)');

		if ($RefID > 0) {
			$field = db::execSQL("
                SELECT signature
                  FROM webset.std_iepparticipants
                 WHERE spirefid = " . $RefID . "
            ")->getOne();
			if ($field != "") {
				$signature->value(FileUtils::createTmpFile(base64_decode($field), 'png'));
			}
		}

		$edit->addControl('Signature Container')
			->name('signature')
			->sqlField('signature')
			->hide();
	}

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl('Doc Area', 'hidden')->value($area)->sqlField('docarea');

    $edit->firstCellWidth = '15%';

    $edit->finishURL = CoreUtils::getURL('meet_participants.php', array('dskey' => $dskey, 'area' => $area));
    $edit->cancelURL = CoreUtils::getURL('meet_participants.php', array('dskey' => $dskey, 'area' => $area));

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

    function setSignature(url) {
	    api.ajax.post(
		    'meet_participants.ajax.php',
		    {'url': url},
		    function(answer) {
			    $('#signature').val(answer.content);
		    }
	    )
    }
</script>
