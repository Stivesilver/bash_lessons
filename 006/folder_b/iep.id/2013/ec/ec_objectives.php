<?php

	Security::init();

	$dskey = io::get('dskey');
	$goal = io::geti('goal', true);

	
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');	
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$area_id = IDEAAppArea::ID_EC_OBJECTIVES;

	$list = new listClass();

	$list->setMasterRecordID($goal);

	$list->title = 'Objectives/Benchmarks';

	$list->SQL = "
		SELECT std.refid,
		       order_num,
			   txt01,
			   txt02,
			   dat01
		  FROM webset.std_general std
		 WHERE stdrefid = " . $tsRefID . " 
		   AND iepyear = " . $stdIEPYear . "
		   AND area_id = " . $area_id . "
		   AND int01 = " . $goal . "			 
		 ORDER BY order_num, 3
	";

	$list->addColumn('Order #');
	$list->addColumn('Objectives/Benchmarks');
	$list->addColumn('Expected Progress');
	$list->addColumn('Target Date')->type('date');

	$list->addURL = CoreUtils::getURL('ec_objectives_add.php', array('dskey' => $dskey, 'goal' => $goal));
    $list->editURL = CoreUtils::getURL('ec_objectives_add.php', array('dskey' => $dskey, 'goal' => $goal));

	$list->deleteTableName = 'webset.std_general';
	$list->deleteKeyField = 'refid';

	$list->addRecordsResequence(
		'webset.std_general',
		'order_num'
	);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();
?>