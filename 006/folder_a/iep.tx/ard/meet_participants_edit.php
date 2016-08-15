<?php

	Security::init();

	$dskey      = io::get('dskey');
	$area       = io::get('area');
	$ds         = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$RefID      = io::geti('RefID');

	$edit = new EditClass("edit1", $RefID);

	$edit->setSourceTable('webset.std_iepparticipants', 'spirefid');

	$edit->title = "Add IEP Meeting Participants";

	$edit->addGroup("General Information");
	$edit->addControl("Participant", "select")
		->sql("
			SELECT coalesce(gdLNm,'') || ' ' || coalesce(gdFNm,''),
                   coalesce(gdLNm,'') || ' ' || coalesce(gdFNm,'')
              FROM webset.dmg_guardianmst
                   INNER JOIN webset.def_guardiantype ON webset.dmg_guardianmst.gdType = webset.def_guardiantype.gtRefID
             WHERE stdRefID = (SELECT stdRefID from webset.sys_teacherstudentassignment where tsrefid = $tsRefID)
			 UNION
			SELECT sys_usermst.umfirstname || ' ' || sys_usermst.umlastname,
                   sys_usermst.umfirstname || ' ' || sys_usermst.umlastname
              FROM sys_usermst
             WHERE sys_usermst.vndrefid = VNDREFID
             ORDER BY 1
            ")
		->sqlField('participantname');

	if (IDEACore::disParam(35) == 'Y') {
		$SQL = "
			SELECT prddesc, prddesc
              FROM webset.disdef_participantrolesdef
		     WHERE vndrefid = VNDREFID
			 ORDER BY seq_num, PRDDesc
			 ";
	} else {
		$SQL = "
			SELECT prddesc, prddesc
              FROM webset.statedef_participantrolesdef
		     WHERE screfid = " . VNDState::factory()->id . "
			 ORDER BY CASE WHEN substring(prddesc,1,1)='*' THEN 1 ELSE 2 END, prddesc
			";
	}

	$edit->addControl("Role", "select")
		->emptyOption(true)
		->sqlField('participantrole')
		->name('participantrole')
		->sql($SQL)
		->req()
		->onChange('getSeq()');

	$edit->addControl("Sequence Number", 'edit')
		->name('std_seq_num')
		->sqlField('std_seq_num');

	$edit->addControl(FFSwitchYN::factory('Agreement'))
		->sqlField('partcat')
		->data(
			array(
				1 => 'Agree',
				2 => 'Disagree'
		)
	);

	$edit->addUpdateInformation();

	$edit->addControl("tsRefID", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("iep year", "hidden")
		->value($stdIEPYear)
		->sqlField('iep_year');

	$edit->addControl("docarea", "hidden")
		->value($area)
		->sqlField('docarea');

    $edit->firstCellWidth = "15%";

	$edit->printEdit();

?>

<script type="text/javascript">

	function getSeq() {
		var role = $('#participantrole').val();
		api.ajax.post(
			'meet_participants_ajax.php',
			{'role': role},
			function(answer) {
				if (answer.seqNum == 0){
					answer.seqNum = '';
				}

				$('#std_seq_num').val(answer.seqNum);
			}
		);
	}

</script>