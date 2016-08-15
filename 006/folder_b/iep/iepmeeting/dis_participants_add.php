<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$RefID = io::geti('RefID');
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$set_ini = IDEAFormat::getIniOptions();
	$iepmode = $set_ini['iep_participants_linked_to_iep_year'] == 'no' ? false : true;

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

	$edit->addControl(FFSelect::factory('Role'))
		->name('dis_role_id')
		->emptyOption(true)
		->sqlField('dis_role_id')
		->sql("
		   SELECT prdrefid,
				  prddesc
             FROM webset.disdef_participantrolesdef
            WHERE vndrefid = VNDREFID
            ORDER BY seq_num
        ")
		->req(true);

	$edit->addControl('Other')
		->sqlField('participantrole')
		->showIf('dis_role_id', db::execSQL("
                                  SELECT prdrefid
                                    FROM webset.disdef_participantrolesdef
								     WHERE substring(lower(prddesc), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl(FFSelect::factory('Attendance Type'))
		->name('partic_type_id')
		->sqlField('partic_type_id')
		->sql("
			SELECT patrefid,
			       patdesc
              FROM webset.statedef_participantattendancetypes
             WHERE screfid = " . VNDState::factory()->id . "
             ORDER BY pat_seq, patdesc
        ");

	$edit->addControl('Other')
		->sqlField('participantatttype')
		->showIf('partic_type_id', db::execSQL("
                                  SELECT patrefid
                                    FROM webset.statedef_participantattendancetypes
								     WHERE substring(lower(patdesc), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

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
	if ($iepmode) $edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('iep_year');
	$edit->addControl('Data Storage Key', 'hidden')->name('dskey')->value($dskey);


	$edit->firstCellWidth = '15%';

	$edit->finishURL = CoreUtils::getURL('dis_participants_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('dis_participants_list.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
<script type="text/javascript">
	function selectUser() {
		var wnd = api.window.open('Find Teacher or Guardian', api.url('iep_participants_users.php', {'dskey': $("#dskey").val()}));
		wnd.resize(950, 600);
		wnd.center();
		wnd.addEventListener('user_selected', onEvent);
		wnd.show();
	}

	function onEvent(e) {
		var name = e.param.name;
		$("#participantname").val(name);
	}

	function setSignature(url) {
		api.ajax.post(
			'iep_participants.ajax.php',
			{'url': url},
			function (answer) {
				$('#signature').val(answer.content);
			}
		)
	}
</script>
