<?php

	Security::init();

	$dskey = io::get('dskey');
	$age = io::get('age');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	$list = new ListClass();

	$list->title = 'Educational Environment';

	$list->SQL = "
        SELECT std.pcrefid,
               state.spccode,
               state.spcdesc,
			   spcbeg,
               spcend
          FROM webset.std_placementcode std
               INNER JOIN webset.statedef_placementcategorycode state ON std.spcrefid=state.spcrefid
         WHERE std.stdrefid = " . $tsRefID . "			 
         ORDER BY spccode
    ";

	$list->addColumn('Code')->sqlField('spccode');
	$list->addColumn('Educational Environment')->sqlField('spcdesc');
	$list->addColumn('Start Date')->sqlField('spcbeg')->type('date');
	$list->addColumn('End Date')->sqlField('spcend')->type('date');

	$list->addURL = CoreUtils::getURL('place_cat_add.php', array('dskey' => $dskey, 'age' => $age));
	$list->editURL = CoreUtils::getURL('place_cat_add.php', array('dskey' => $dskey, 'age' => $age));

	$list->addRecordsProcess('Delete')
		->message('Do you really want to delete this Educational Environment?')
		->url(CoreUtils::getURL('place_cat_delete.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_placementcode')
			->setKeyField('pcrefid')
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();
?>
