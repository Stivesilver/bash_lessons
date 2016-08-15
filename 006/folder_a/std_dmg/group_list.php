<?php

    Security::init();

    $stdrefid = io::get('stdrefid');

    $list = new ListClass();
    $list->title = 'Groupings';

    $list->SQL = "
        SELECT sgdrefid, 
	           sdgname 
		  FROM webset.dmg_studentgroupingdtl std
			   INNER JOIN webset.disdef_stddemogrouping dis ON std.sdgrefid = dis.sdgrefid
    	 WHERE stdrefid = $stdrefid
		 ORDER BY sdgname
    ";

    $list->addColumn('Group');

    $list->addURL = CoreUtils::getURL('group_add.php', array('stdrefid' => $stdrefid));
    $list->editURL = CoreUtils::getURL('group_add.php', array('stdrefid' => $stdrefid));

    $list->deleteTableName = 'webset.dmg_studentgroupingdtl';
    $list->deleteKeyField = 'sgdrefid';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();
?>
