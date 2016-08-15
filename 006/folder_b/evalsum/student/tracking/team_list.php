<?php

	Security::init();

	$dskey = io::get('dskey');
	$eprefid = io::geti('eprefid');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass('list1');

	$list->title = 'Evaluation Team Members';

	$list->SQL = "
		SELECT spirefid,
			   participantname,
			   participantrole,			   
			   std_seq_num
		  FROM webset.es_std_evalproc_part
		 WHERE evalproc_id = " . $eprefid . "
		 ORDER BY std_seq_num, participantname
    ";

	$list->addColumn('Participant');
	$list->addColumn('Role');	
	$list->addColumn('Order #');

	$list->addURL = CoreUtils::getURL('team_add.php', array('dskey' => $dskey, 'eprefid' => $eprefid));
	$list->editURL = CoreUtils::getURL('team_add.php', array('dskey' => $dskey, 'eprefid' => $eprefid));

	$list->deleteTableName = 'webset.es_std_evalproc_part';
	$list->deleteKeyField = 'spirefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();
?>