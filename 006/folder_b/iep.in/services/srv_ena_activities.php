<?php

    Security::init();

    $dskey = io::get('dskey');
    $tsRefID = DataStorage::factory($dskey)->safeGet('tsRefID');

    $list = new ListClass();

    $list->title = 'Student Extracurricular and Non-Academic Activities';
	
    $list->SQL = "
        SELECT siearefid,
			   sieanarrtext
	  	  FROM webset.std_in_ena_activities
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY sieadrefid, siearefid
    ";


    $list->addColumn('Narrative');

    $list->addURL = CoreUtils::getURL('srv_ena_activities_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('srv_ena_activities_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_in_ena_activities';
    $list->deleteKeyField = 'siearefid';

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