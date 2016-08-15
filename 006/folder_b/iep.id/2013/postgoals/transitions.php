<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$path = '/apps/idea/iep.id/2013/postgoals/by_year_transitions_list.php';

	$area_id = IDEAAppArea::ID_SEC_TRANS_ACTIVITIES;

	$list = new listClass();

	$list->title = 'Transition Activities';
	$list->SQL = "
		SELECT std.refid,
		       order_num,
			   area.validvalueid || '. ' || area.validvalue,
			   dsydesc,
			   txt01,
			   txt02,
			   dat01,
			   status.sequence_number || COALESCE(' ' || txt03, '') as status,
			   dat02
		  FROM webset.std_general std
			   INNER JOIN webset.glb_validvalues area ON area.refid = std.int01
			   INNER JOIN webset.glb_validvalues status ON status.refid = std.int02
			   LEFT OUTER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = std.int03
		 WHERE iepyear = " . $stdIEPYear . "
		   AND area_id = " . $area_id . "
		 ORDER BY COALESCE(order_num), area.sequence_number, dat01, std.refid
	";

	$list->addColumn('Order #');
	$list->addColumn('Transition Activities');
	$list->addColumn('School Year');
	$list->addColumn('Description');
	$list->addColumn('Position Responsible');
	$list->addColumn('Start Date')->type('date');
	$list->addColumn('Status');
	$list->addColumn('Completion Date')->type('date');

	$list->addURL = CoreUtils::getURL('transitions_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('transitions_add.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.std_general';
	$list->deleteKeyField = 'refid';

	$button = new IDEAPopulateIEPYear($dskey, $area_id, $path);
	$listButton = $button->getPopulateButton();
	$list->addButton($listButton);

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
