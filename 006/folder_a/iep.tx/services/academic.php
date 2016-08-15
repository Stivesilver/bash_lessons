<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Academic Schedule';

	$list->SQL = "
		SELECT refid,
			   semester_txt,
			   course,
			   CASE spfreq.frequency WHEN 'Other:' THEN COALESCE(spedfreq_oth, '') ELSE spfreq.frequency END || ' ' ||
			   CASE spdur.duration WHEN 'Other:' THEN COALESCE(spedduration_oth, '') ELSE spdur.duration END,
			   CASE sploc.location WHEN 'Other:' THEN COALESCE(spedloc_oth, '') ELSE sploc.location END ,
			   CASE genfreq.frequency WHEN 'Other:' THEN COALESCE(genfreq_oth, '') ELSE genfreq.frequency END || ' ' ||
			   CASE gendur.duration WHEN 'Other:' THEN COALESCE(genduration_oth, '') ELSE gendur.duration END,
			   CASE genloc.location WHEN 'Other:' THEN COALESCE(genloc_oth, '') ELSE genloc.location END ,
			   order_num
		  FROM webset_tx.std_srv_courses std
			   INNER JOIN webset_tx.def_srv_frequency spfreq ON spfreq.frefid = std.spedfreq
			   INNER JOIN webset_tx.def_srv_duration spdur ON spdur.drefid = std.spedduration
			   INNER JOIN webset_tx.def_srv_locations sploc ON sploc.lrefid = std.spedloc
			   INNER JOIN webset_tx.def_srv_frequency genfreq ON genfreq.frefid = std.genfreq
			   INNER JOIN webset_tx.def_srv_duration gendur ON gendur.drefid = std.genduration
			   INNER JOIN webset_tx.def_srv_locations genloc ON genloc.lrefid = std.genloc
		 WHERE stdrefid = " . $tsRefID . "
		   AND iep_year = " . $stdIEPYear . "
		 ORDER BY order_num, refid
    ";

	$list->addColumn('Year')->type('group');
	$list->addColumn('Course/Curriculum Area');
	$list->addColumn('Sp. Ed. Time');
	$list->addColumn('Sp. Ed. Location');
	$list->addColumn('Gen. Ed. Time');
	$list->addColumn('Gen. Ed. Location');
	$list->addColumn('Order #');

	$list->addURL = CoreUtils::getURL('academic_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('academic_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset_tx.std_srv_courses';
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
				UIAnchor::factory('Additional Services Information')
				->onClick('api.window.open("Additional Services Information", ' . json_encode(CoreUtils::getURL('/apps/idea/iep/constructions/main.php', array('dskey' => $dskey, 'constr' => 117))) . ')')
			)->toHTML();
?>
