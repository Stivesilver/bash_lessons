<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$list = new ListClass();

	$list->title = "Observation";

	$list->SQL = "
        SELECT erob_refid,
               obs.observer,
               obs.role,
               obs.location,
               obs.date,
               obs.time,
               summary
          FROM webset.es_std_er_observation AS obs
         WHERE eprefid = " . $evalproc_id . "
         ORDER BY order_num
    ";

	$list->addColumn("Observer")->sqlField('observer');
	$list->addColumn("Role")->sqlField('role');
	$list->addColumn("Location")->sqlField('location');
	$list->addColumn("Date")->sqlField('date')->type('date');
	$list->addColumn("Time")->sqlField('time');
	$list->addColumn("Observation")
		->sqlField('summary')
		->css('overflow', 'hidden')
		->css('text-overflow', 'ellipsis')
		->css('max-width', '300px')
		->css('white-space', 'nowrap');

	$list->addRecordsResequence('webset.es_std_er_observation', 'order_num');

	$list->addURL = CoreUtils::getURL('./observation_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('./observation_edit.php', array('dskey' => $dskey));

	$list->deleteTableName = 'webset.es_std_er_observation';
	$list->deleteKeyField = 'erob_refid';

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
