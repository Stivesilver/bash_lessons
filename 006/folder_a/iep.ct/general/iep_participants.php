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
            SELECT spirefid ,
                   participantname,
                   seq_num || '. ' || CASE WHEN lower(prddesc) LIKE '%other%' THEN prddesc || ' ' || COALESCE(participantrole, '') ELSE prddesc END AS pcname,
                   seq_num
              FROM webset.std_iepparticipants AS partc
				   INNER JOIN webset.statedef_participantrolesdef AS rol ON (rol.prdrefid = partc.role_id)
             WHERE stdrefid = " . $tsRefID . "
               " . $where . "
             ORDER BY seq_num, participantname
    ";

	$list1->addColumn('Participant')->sqlField('participantname');
	$list1->addColumn('Role')->sqlField('pcname');
	$list1->addColumn('Sequence Number')->sqlField('seq_num');

	$list1->addURL = CoreUtils::getURL('iep_participants_add.php', array('dskey' => $dskey));
	$list1->editURL = CoreUtils::getURL('iep_participants_add.php', array('dskey' => $dskey));

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

	$role_ext = db::execSQL("
		SELECT prdrefid
			  FROM webset.statedef_participantrolesdef AS rol
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND NOT EXISTS (SELECT 1 FROM webset.std_iepparticipants AS partc WHERE partc.role_id = rol.prdrefid $where)
	")->getOne();

	if (!$role_ext) {
		$list1->getButton(ListClassButton::ADD_NEW)
			->disabled(true);
	}

	$list1->printList();
?>
