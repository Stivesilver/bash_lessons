<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    $list = new ListClass();

    $list->printable = TRUE;

    $list->SQL = "
		SELECT cnrefid,
			   eventdt,
			   eventdt,
			   cnsdesc,
			   entryuser,
			   std.lastupdate
		  FROM webset.std_casenotes std			   
		 WHERE stdrefid = " . $tsRefID . "
			   ADD_SEARCH
		 ORDER BY eventdt, cnsdesc
	";

    $list->title = "Case Notes";
    $list->showSearchFields = true;

    $list->addColumn("Date of Event")->type('date');
    $list->addColumn("Time")->type('time');
    $list->addColumn("Short Description");
    $list->addColumn("Recorded by");
    $list->addColumn("Date of Entry")->type('date');

    $list->addURL = CoreUtils::getURL('cn_casenotes_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('cn_casenotes_add.php', array('dskey' => $dskey));

    $list->deleteTableName = "webset.std_casenotes";
    $list->deleteKeyField = "cnrefid";

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();
?>
