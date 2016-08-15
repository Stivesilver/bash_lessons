<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$list = new ListClass('list1');

	$list->title = 'Eligibility Determination Team';

	$list->SQL = "
		SELECT erpa_refid,
			   pt.part_name,
			   CASE WHEN lower(role) LIKE '%other%' THEN COALESCE(pt.part_role_oth, role) ELSE role END AS part_role,
			   dissent_attached_sw
		  FROM webset.es_std_er_participants AS pt
			   INNER JOIN webset.es_statedef_red_part AS spt ON (pt.part_role_id = spt.refid)
		 WHERE eprefid = $evalproc_id
		 ORDER BY seq, 2
    ";

	$list->addColumn('Name')->sqlField('part_name');
	$list->addColumn('Role')->sqlField('part_role');

	$list->addURL = CoreUtils::getURL('./edt_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('./edt_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.es_std_er_participants';
	$list->deleteKeyField = 'erpa_refid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode('list1')
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();
?>
