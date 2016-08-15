<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$set_ini = IDEAFormat::getIniOptions();
	$iepmode = $set_ini['iep_participants_linked_to_iep_year'] == 'no' ? false : true;

	$list1 = new ListClass('list1');

	$list1->title = 'IEP Meeting Participants';

	$where = ($iepmode) ? 'AND iep_year = ' . $stdIEPYear : '';

	$list1->SQL = "
            SELECT spirefid,
           	 	   participantname,
                   CASE WHEN lower(prddesc) LIKE '%other%' AND  participantrole != '' THEN COALESCE(participantrole, '') ELSE prddesc END,
                   CASE WHEN lower(patdesc) LIKE '%other%' AND  participantatttype != '' THEN COALESCE(participantatttype, '') ELSE patdesc END,
                   seq_num
              FROM webset.std_iepparticipants AS pt
				   INNER JOIN webset.disdef_participantrolesdef AS dpt ON (pt.dis_role_id = dpt.prdrefid)
				   INNER JOIN webset.statedef_participantattendancetypes AS pttype ON (pt.partic_type_id = pttype.patrefid)
             WHERE stdrefid = " . $tsRefID . "
               " . $where . "
             ORDER BY seq_num, participantname
    ";

	$list1->addColumn('Participant');
	$list1->addColumn('Role');
	$list1->addColumn('Attendance Type');

	$list1->addURL = CoreUtils::getURL('dis_participants_add.php', array('dskey' => $dskey));
	$list1->editURL = CoreUtils::getURL('dis_participants_add.php', array('dskey' => $dskey));

	$list1->deleteTableName = 'webset.std_iepparticipants';
	$list1->deleteKeyField = 'spirefid';

	$list1->addButton(
		FFIDEAExportButton::factory()
			->setTable($list1->deleteTableName)
			->setKeyField($list1->deleteKeyField)
			->applyListClassMode('list1')
	);

	$list1->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$default_participnats_count = db::execSQL("
	    SELECT count(1)
          FROM webset.disdef_validvalues
         WHERE valuename = 'DefaultParticipants'
           AND vndrefid = VNDREFID
	")->getOne();

	if ($default_participnats_count > 0) {

		$student = IDEAStudent::factory($tsRefID);
		$guardians = $student->getGuardians();
		$stdname = $student->get('stdfirstname') . ' ' . $student->get('stdlastname');
		$guardian = count($guardians) > 0 ? $guardians[0]['gdfnm'] . ' ' . $guardians[0]['gdlnm'] : '';

		$pname = "
			CASE (xpath('/record/name/text()', validvalue::XML))[1]::varchar
				WHEN 'STUDENT' THEN '" . db::escape($stdname) . "'
				WHEN 'GUARDIAN' THEN '" . db::escape($guardian) . "'
				ELSE (xpath('/record/name/text()', validvalue::XML))[1]::varchar
			END
		";

		$list1->addButton(
			IDEAPopulateWindow::factory()
				->addNewItem()
				->setTitle('Participant Defaults')
				->setSQL("
					SELECT refid,
					       $pname AS pname,
					       prddesc AS prole,
					       patdesc AS ptype
					  FROM webset.disdef_validvalues AS dv
						   INNER JOIN webset.disdef_participantrolesdef AS dpt ON ((xpath('/record/role/text()', validvalue::XML))[1]::VARCHAR = dpt.prdrefid::VARCHAR)
						   INNER JOIN webset.statedef_participantattendancetypes AS pttype ON ((xpath('/record/type/text()', validvalue::XML))[1]::VARCHAR = pttype.patrefid::VARCHAR)
					 WHERE valuename = 'DefaultParticipants'
					   AND dv.vndrefid = VNDREFID
					 ORDER BY sequence_number, validvalue
                ")
				->addColumn('Participant')
				->addColumn('Role')
				->addColumn('Attendance Type')
				->setDestinationTable('webset.std_iepparticipants')
				->setDestinationTableKeyField('spirefid')
				->setSourceTable('webset.disdef_validvalues')
				->setSourceTableKeyField('refid')
				->addPair('stdrefid', $tsRefID, false)
				->addPair('iep_year', $stdIEPYear, false)
				->addPair('lastuser', SystemCore::$userUID, false)
				->addPair('lastupdate', 'NOW()', true)
				->addPair('participantname', $pname)
				->addPair('dis_role_id', "(xpath('/record/role/text()', validvalue::XML))[1] AS prole", true)
				->addPair('partic_type_id', "(xpath('/record/type/text()', validvalue::XML))[1] AS ptype", true)
				->getPopulateButton()
		);
	}

	$list1->printList();

	if (IDEACore::disParam(18) != 'N' or db::execSQL("
                SELECT count(1)
                  FROM webset.std_iepmeetingparticipantscomments
                 WHERE stdrefid = " . $tsRefID . "
               " . $where . "
        ")->getOne() > 0
	) {

		$list2 = new ListClass('list2');

		$list2->title = 'IEP Meeting Participation Comments';

		$list2->SQL = "
            SELECT simpcrefid,
                   impctext || COALESCE(' <i>' || simpcnarrtext, '')
              FROM webset.std_iepmeetingparticipantscomments  std
                   INNER JOIN webset.statedef_iepmeetpartcomment stt ON stt.impcrefid = std.impcrefid
             WHERE stdrefid = " . $tsRefID . "
               " . $where . "
        ";

		$list2->addColumn('IEP Meeting Participation Comment');
		$list2->addURL = CoreUtils::getURL('iep_part_comm_add.php', array('dskey' => $dskey));
		$list2->editURL = CoreUtils::getURL('iep_part_comm_add.php', array('dskey' => $dskey));

		$list2->deleteTableName = 'webset.std_iepmeetingparticipantscomments';
		$list2->deleteKeyField = 'simpcrefid';

		$list2->addButton(
			FFIDEAExportButton::factory()
				->setTable($list2->deleteTableName)
				->setKeyField($list2->deleteKeyField)
				->applyListClassMode('list2')
		);

		$list2->printList();
	}
?>
