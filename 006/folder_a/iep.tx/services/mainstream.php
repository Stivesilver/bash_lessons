<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Mainstream Instructional Setting';

	$list->SQL = "
		SELECT refid,
			   service || COALESCE (' - ' || servicetxt, ''),
			   to_char(startdate,'MM-DD-YYYY'),
			   CASE WHEN freq.frequency like 'Other%' THEN COALESCE('<i>' || freq_oth || '</i>', '') ELSE freq.frequency END,
			   CASE WHEN loc.location 	like 'Other%' THEN COALESCE('<i>' || loc_oth || '</i>', '') ELSE loc.location END,
			   CASE WHEN dur.duration 	like 'Other%' THEN COALESCE('<i>' || duration_oth || '</i>', '') ELSE dur.duration END
		  FROM webset_tx.std_srv_mainstream std
			   INNER JOIN webset_tx.def_srv_mainstream rel ON mrefid = srefid
			   INNER JOIN webset_tx.def_srv_frequency freq ON freq.frefid = std.freq
			   INNER JOIN webset_tx.def_srv_duration dur ON dur.drefid = std.duration
			   INNER JOIN webset_tx.def_srv_locations loc ON loc.lrefid = std.loc
		 WHERE stdrefid = " . $tsRefID . "
		   AND iep_year = " . $stdIEPYear . "
		 ORDER BY 1, rel.seqnum
    ";

	$list->addColumn('Service Type');
	$list->addColumn('Start Date');
	$list->addColumn('Frequency');
	$list->addColumn('Location');
	$list->addColumn('Duration');

	$list->addURL = CoreUtils::getURL('mainstream_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('mainstream_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset_tx.std_srv_mainstream';
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
	
	$list->printList();
	
?>
