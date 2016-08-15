<?

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	if ($RefID == '') {
		$list = new ListClass();

		$list->title = 'Team Conclusions and Decisions - Participants';

		$list->SQL = "
            SELECT std.refid,
                   part_name,
                   COALESCE(other, role)
              FROM webset.es_statedef_red_part state
                   INNER JOIN webset.es_std_red_part std ON state.role = std.part_role
             WHERE stdrefid = " . $tsRefID . "
               AND evalproc_id = $evalproc_id
             ORDER BY COALESCE(state.seq, 0)
        ";

		$list->addColumn('Name');
		$list->addColumn('Role');

		$list->addURL = CoreUtils::getURL('participants.php', array('dskey' => $dskey));
		$list->editURL = CoreUtils::getURL('participants.php', array('dskey' => $dskey));

		$list->deleteTableName = 'webset.es_std_red_part';
		$list->deleteKeyField = 'refid';

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable($list->deleteTableName)
				->setKeyField($list->deleteKeyField)
				->applyListClassMode()
		);

		$list->addButton(
			IDEAFormat::getPrintButton(array('dskey' => $dskey))
		);

		$student = IDEAStudent::factory($tsRefID);

		$guardians = $student->getGuardians();
		$guardian = count($guardians) > 0 ? $guardians[0]['gdfnm'] . ' ' . $guardians[0]['gdlnm'] : '';
		$pname = "
			CASE
				WHEN role ILIKE '%Parent%' THEN '" . db::escape($guardian) . "'
			END
		";

		$list->addButton(
			IDEAPopulateWindow::factory()
				->addNewItem()
				->setTitle('Participant Defaults')
				->setSQL("
					SELECT refid, $pname, role
					  FROM webset.es_statedef_red_part
					 ORDER BY seq, role
				")
				->addColumn('Participant')
				->addColumn('Role')
				->setDestinationTable('webset.es_std_red_part')
				->setDestinationTableKeyField('refid')
				->setSourceTable('webset.es_statedef_red_part')
				->setSourceTableKeyField('refid')
				->addPair('stdrefid', $tsRefID, false)
				->addPair('evalproc_id', $evalproc_id, false)
				->addPair('lastuser', SystemCore::$userUID, false)
				->addPair('lastupdate', 'NOW()', true)
				->addPair('part_name', $pname)
				->addPair('part_role', 'role', true)
				->addPair('seq', 'seq', true)
				->addNewItem()
				->setTitle('IEP Participants')
				->setSQL("
					SELECT spirefid ,
						   participantname ,
						   participantrole
					  FROM webset.std_iepparticipants
					 WHERE stdRefID = " . $tsRefID . "
					   AND iep_year = " . (int)$student->get('stdiepyear') . "
					   AND COALESCE(docarea, 'I') = 'I'
					 ORDER BY CASE WHEN substring(participantrole,1,1)='*' THEN 1 ELSE 2 END, std_seq_num, participantname
				")
				->addColumn('Participant')
				->addColumn('Role')
				->setDestinationTable('webset.es_std_red_part')
				->setDestinationTableKeyField('refid')
				->setSourceTable('webset.std_iepparticipants')
				->setSourceTableKeyField('spirefid')
				->addPair('stdrefid', $tsRefID, false)
				->addPair('evalproc_id', $evalproc_id, false)
				->addPair('lastuser', SystemCore::$userUID, false)
				->addPair('lastupdate', 'NOW()', true)
				->addPair('part_name', 'participantname', true)
				->addPair('part_role', 'Other', false)
				->addPair('other', 'participantrole', true)
				->addPair('seq', 'std_seq_num', true)
				->getPopulateButton()
		);


		$list->printList();
	} else {

		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Team Conclusions and Decisions - Participant';

		$edit->setSourceTable('webset.es_std_red_part', 'refid');

		$edit->addGroup('General Information');

		$edit->addControl('Name')
			->sqlField('part_name')
			->size(50);

		$edit->addControl('Role:', 'select')
			->name('part_role')
			->sqlField('part_role')
			->sql("
                SELECT role, role
                  FROM webset.es_statedef_red_part
                 ORDER BY seq, role
            ")
			->emptyOption(true);

		$edit->addControl('Specify')
			->name('other')
			->sqlField('other')
			->showIf('part_role', 'Other')
			->size(50);

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
		$edit->addControl("evalproc_id", "hidden")
			->value($evalproc_id)
			->sqlField('evalproc_id');

		$edit->finishURL = CoreUtils::getURL('participants.php', array('dskey' => $dskey));
		$edit->cancelURL = CoreUtils::getURL('participants.php', array('dskey' => $dskey));
		$edit->saveAndAdd = true;

		$edit->printEdit();
	}
?>
