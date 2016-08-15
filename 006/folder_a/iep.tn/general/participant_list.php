<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$set_ini = IDEAFormat::getIniOptions();
	$iepmode = $set_ini['iep_participants_linked_to_iep_year']=='no' ? false : true;

	$list1 = new ListClass('list1');

	$list1->title = $set_ini['iep_title'] . ' Team Member';

	$where = ($iepmode) ? 'AND iep_year = ' . $stdIEPYear : '';

	$list1->SQL = "
            SELECT spirefid ,
                   participantname ,
                   participantrole ,
                   participantatttype,
                   participantdate,
                   participantagree,
                   participantcomment,
                   std_seq_num
              FROM webset.std_iepparticipants
             WHERE stdrefid = " . $tsRefID . "
               " . $where . "
             ORDER BY std_seq_num, participantname
    ";

	$list1->addColumn('IFSP Team Member')->sqlField('participantname');
	$list1->addColumn('Agency/Title')->sqlField('participantrole');
	$list1->addColumn('Date')->sqlField('participantdate')->type('date');
	$list1->addColumn('Contributed/not present/method')->sqlField('participantatttype');
	$list1->addColumn('Fully Agree')->sqlField('participantagree')->type('switch');
	$list1->addColumn('Area(s) of Concerns/Comments')->sqlField('participantcomment');

	$list1->addRecordsResequence('webset.std_iepparticipants', 'std_seq_num');

	$list1->addURL = CoreUtils::getURL('./participant_add.php', array('dskey' => $dskey));
	$list1->editURL = CoreUtils::getURL('./participant_add.php', array('dskey' => $dskey));

	$list1->deleteTableName = 'webset.std_iepparticipants';
	$list1->deleteKeyField = 'spirefid';

	$list1->addButton(
		FFIDEAExportButton::factory()
			->setTable($list1->deleteTableName)
			->setKeyField($list1->deleteKeyField)
			->applyListClassMode('list1')
	);

	$list1->addButton(
		FFIDEAHelpButton::factory()
			->setHTMLByConstruction(179)
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
		                   (xpath('/record/role/text()', validvalue::XML))[1] AS prole,
		                   (xpath('/record/type/text()', validvalue::XML))[1] AS ptype,
		                   sequence_number
		              FROM webset.disdef_validvalues
		             WHERE valuename = 'DefaultParticipants'
		               AND vndrefid = VNDREFID
		             ORDER BY sequence_number, validvalue
                ")
				->addColumn('Participant')
				->addColumn('Role')
				->addColumn('Sequence Number')
				->setDestinationTable('webset.std_iepparticipants')
				->setDestinationTableKeyField('spirefid')
				->setSourceTable('webset.disdef_validvalues')
				->setSourceTableKeyField('refid')
				->addPair('stdrefid', $tsRefID, false)
				->addPair('iep_year', $stdIEPYear, false)
				->addPair('lastuser', SystemCore::$userUID, false)
				->addPair('lastupdate', 'NOW()', true)
				->addPair('participantname', $pname)
				->addPair('participantrole', "(xpath('/record/role/text()', validvalue::XML))[1] AS prole", true)
				->addPair('participantatttype', "(xpath('/record/type/text()', validvalue::XML))[1] AS ptype", true)
				->addPair('std_seq_num', 'sequence_number', true)
				->getPopulateButton()
		);
	}

	$list1->printList();
?>
