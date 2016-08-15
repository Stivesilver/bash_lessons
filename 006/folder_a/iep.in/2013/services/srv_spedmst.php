<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Special Education Services';

	$list->SQL = "
		SELECT sped.ssmrefid,
			   stsdesc || CASE stsndpsrvsw WHEN 'Y' THEN '/Non-District' ELSE '' END,
			   CASE WHEN LOWER(tsndesc) LIKE (LOWER('%other')) THEN tsndesc || ': ' || stsother
			   ELSE tsndesc END AS tsndesc,
			   ssmbegdate,
			   ssmenddate,
			   COALESCE(sped.ssmtime, ' ')  || '  '|| COALESCE(sadesc, ' ') ||' '|| COALESCE(sfDesc, ' '),
			   CASE WHEN LOWER(crtdesc) LIKE (LOWER('%other%')) THEN 'Other: ' || sped.loc_oth
			   ELSE crtdesc END,
			   weeks
		  FROM webset.std_srv_sped sped
			   INNER JOIN webset.statedef_services_all ON sped.stsRefID = webset.statedef_services_all.stsRefID
			   LEFT OUTER JOIN webset.statedef_services_type ON sped.srv_cat = webset.statedef_services_type.trefid
			   LEFT OUTER JOIN sys_usermst ON sped.umrefid = sys_usermst.umrefid
			   INNER JOIN webset.def_spedfreq ON sfRefID = sped.ssmFreq
			   INNER JOIN webset.def_spedamt ON saRefID = sped.ssmAmt
			   INNER JOIN webset.def_classroomtype ON sped.ssmClassType = webset.def_classroomtype.crtRefID
			   INNER JOIN webset.disdef_tsn ON sped.srv_class = webset.disdef_tsn.tsnrefid
		 WHERE sped.stdrefid = " . $tsRefID . "
		   AND COALESCE(esy,'N')= '" . io::get('ESY') . "'
		 ORDER BY typedesc, stsDesc, sped.ssmBegDate, stsdesc, stsother
	";

	$list->addColumn('Type');
	$list->addColumn('Sp Ed Service/Class');
	$list->addColumn('Start Date')->type('date');
	$list->addColumn('End Date')->type('date');
	$list->addColumn('Frequency');
	$list->addColumn('Location');
	if (io::get('ESY') == 'Y') $list->addColumn('Weeks');

	$list->addURL = CoreUtils::getURL('srv_spedmst_add.php', array('dskey' => $dskey, 'ESY' => io::get('ESY')));
	$list->editURL = CoreUtils::getURL('srv_spedmst_add.php', array('dskey' => $dskey, 'ESY' => io::get('ESY')));

	$list->deleteTableName = 'webset.std_srv_sped';
	$list->deleteKeyField = 'ssmrefid';

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
