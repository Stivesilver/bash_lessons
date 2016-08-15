<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$RefID = io::geti('RefID');
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area = io::get('area');

	//GET ATTENDANCE TYPE DROPDOWN
	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add ARD/IEP Meeting Participants';

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

	$edit->addControl('Order #', 'integer')->sqlField('std_seq_num')->size(20);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('iep_year');
	$edit->addControl("Doc Area", "hidden")->value($area)->sqlField('docarea');
	$edit->addControl('Data Storage Key', 'hidden')->name('dskey')->value($dskey);

	$edit->firstCellWidth = '15%';

	$edit->finishURL = CoreUtils::getURL('meet_participants.php', array('dskey' => $dskey, 'area' => $area));
	$edit->cancelURL = CoreUtils::getURL('meet_participants.php', array('dskey' => $dskey, 'area' => $area));

	$edit->printEdit();
?>
<script type="text/javascript">
		function selectUser() {
			var wnd = api.window.open('Find Teacher or Guardian', api.url('../../iep/iepmeeting/iep_participants_users.php', {'dskey': $("#dskey").val()}));
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
				'iep_participants.ajax.php',
				{'url': url},
			function(answer) {
				$('#signature').val(answer.content);
			}
			)
		}
</script>
