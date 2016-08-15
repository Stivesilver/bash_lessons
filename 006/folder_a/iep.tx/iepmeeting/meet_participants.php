<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area = io::get('area');

	$list1 = new ListClass('list1');

	$list1->title = 'ARD/IEP Meeting Participants';

	$list1->SQL = "
            SELECT spirefid ,
                   participantname ,
                   participantrole ,
                   std_seq_num
              FROM webset.std_iepparticipants
             WHERE stdrefid = " . $tsRefID . "
               AND iep_year = " . $stdIEPYear . "
               AND COALESCE(docarea, 'A') = '" .$area. "'
             ORDER BY CASE WHEN substring(participantrole,1,1)='*' THEN 1 ELSE 2 END, std_seq_num, participantname
    ";

	$list1->addColumn('Participant');
	$list1->addColumn('Role');
	$list1->addColumn('Sequence Number');

	$list1->addURL = CoreUtils::getURL('meet_participants_add.php', array('dskey' => $dskey, 'area' => $area));
	$list1->editURL = CoreUtils::getURL('meet_participants_add.php', array('dskey' => $dskey, 'area' => $area));

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

	$list1->printList();


?>
