<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$RefID = io::geti('RefID');
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$set_ini = IDEAFormat::getIniOptions();
	$iepmode = $set_ini['iep_participants_linked_to_iep_year'] == 'no' ? false : true;

	$where = 'AND partc.stdrefid =' . $tsRefID;
	if ($iepmode) {
		$where .= 'AND iep_year = ' . $stdIEPYear;
	}
	if ($RefID) {
		$where .= 'AND spirefid <>' . $RefID;
	}

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
		->name('role_id')
		->sqlField('role_id')
		->sql("
			SELECT prdrefid,
                   rol.seq_num || '. ' || rol.prddesc
			  FROM webset.statedef_participantrolesdef AS rol
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND NOT EXISTS (SELECT 1 FROM webset.std_iepparticipants AS partc WHERE partc.role_id = rol.prdrefid $where)
			 ORDER BY rol.seq_num, rol.prddesc
		");

	$edit->addControl('Other')
		->sqlField('participantrole')
		->showIf('role_id', db::execSQL("
                                  SELECT prdrefid
                                    FROM webset.statedef_participantrolesdef
								     WHERE substring(lower(prddesc), 1, 5) = 'other'
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
	$role_ext = db::execSQL("
		SELECT count(prdrefid)
			  FROM webset.statedef_participantrolesdef AS rol
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND NOT EXISTS (SELECT 1 FROM webset.std_iepparticipants AS partc WHERE partc.role_id = rol.prdrefid $where)
	")->getOne();

	if ($role_ext <= 1) {
		$edit->getButton(EditClassButton::SAVE_AND_ADD)
			->disabled(true);
	}
	$edit->finishURL = CoreUtils::getURL('iep_participants.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('iep_participants.php', array('dskey' => $dskey));

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
		var title = e.param.title;
		$("#participantname").val(name);
		$("#participantrole").val(title);
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
