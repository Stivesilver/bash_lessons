<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Related Services';

	$list->SQL = "
		SELECT refid,
			   CASE WHEN service 	like 'Other%' THEN COALESCE('<i>' || serv_other || '</i>', '') ELSE service 		END,
			   CASE WHEN frequency 	like 'Other%' THEN COALESCE('<i>' || freq_other || '</i>', '') ELSE frequency 		END || ' ' ||
			   CASE WHEN dur.duration like 'Other%' THEN COALESCE('<i>' || duration_oth || '</i>', '') ELSE dur.duration 	END,
			   CASE WHEN location 	like 'Other%' THEN COALESCE('<i>' || loc_other || '</i>', '') ELSE location 		END
		  FROM webset_tx.std_srv_related std
			   INNER JOIN webset_tx.def_srv_related rel ON rrefid = srefid
			   INNER JOIN webset_tx.def_srv_frequency freq ON freq.frefid = std.freq
			   INNER JOIN webset_tx.def_srv_duration dur ON dur.drefid = std.duration
			   INNER JOIN webset_tx.def_srv_locations loc ON loc.lrefid = std.loc
		 WHERE stdrefid = " . $tsRefID . "
		   AND iep_year = " . $stdIEPYear . "
		 ORDER BY 1, rel.seqnum
    ";

	$list->addColumn('Related Services');
	$list->addColumn('Time');
	$list->addColumn('Location');

	$list->addURL = CoreUtils::getURL('related_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('related_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset_tx.std_srv_related';
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
	
	print UILayout::factory()
			->addHTML('', '2%')
			->addObject(
				UIAnchor::factory('Additional Related Information')
				->onClick('api.window.open("Additional Related Information", ' . json_encode(CoreUtils::getURL('/apps/idea/iep/constructions/main.php', array('dskey' => $dskey, 'constr' => 118))) . ')')
			)->toHTML();
	
?>
