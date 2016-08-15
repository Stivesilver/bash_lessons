<?php

	Security::init();

	$dskey      = io::get('dskey');
	$area       = io::get('area');
	$ds         = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$editUrl    = CoreUtils::getURL(
					  'meet_participants_edit.php',
					   array(
						   'dskey'   => $dskey,
						   'area'    => $area
					   )
	);

	$list = new ListClass();

	$list->multipleEdit    = false;
	$list->deleteTableName = "webset.std_iepparticipants";
	$list->deleteKeyField  = "spirefid";
	$list->addURL          = $editUrl;
	$list->editURL         = $editUrl;
	$list->title           = "IEP Meeting Participants";
	$list->SQL             = "
		SELECT spirefid,
               participantname,
               participantrole,
               std_seq_num,
               CASE partcat WHEN 1 THEN 'Agree' WHEN 2 THEN 'Disagree' END
          FROM webset.std_iepparticipants
         WHERE stdRefID = $tsRefID
          AND iep_year = $stdIEPYear
          AND COALESCE(docarea, 'A') = '$area'
        ORDER BY CASE WHEN substring(participantrole,1,1)='*' THEN 1 ELSE 2 END, std_seq_num, participantname
        ";

	$list->addColumn("Participant");
	$list->addColumn("Role");
	$list->addColumn("Sequence Number");
	$list->addColumn("Agreement");

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

?>