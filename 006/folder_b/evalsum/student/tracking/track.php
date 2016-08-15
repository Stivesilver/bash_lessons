<?php

	Security::init();

	$dskey = io::get('dskey');
	$tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'Student Evaluation Process Tracking';

	$list->SQL = "
		SELECT eprefid, 
		       date_start, 
			   essrtdescription
		  FROM webset.es_std_evalproc
			   INNER JOIN webset.es_statedef_reporttype ON essrtrefid = ev_type
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY date_start desc
    ";

	$list->addColumn('Evaluation Start Date')->type('date');
	$list->addColumn('Evaluation Type');

	$list->addURL = CoreUtils::getURL('track_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('track_add.php', array('dskey' => $dskey));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_evalproc')
			->setKeyField('eprefid')
			->applyListClassMode()
	);

	$list->addRecordsProcess('Delete')
		->message('Do you really want to delete this Process?')
		->url(CoreUtils::getURL('track_delete.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();
?>