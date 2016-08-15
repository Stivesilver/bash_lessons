<?php

    Security::init();

    $list = new ListClass();
    $list->title = "Emergency Contacts";

    $list->SQL = "
        SELECT ec_refid,
	           ec_fname || ' ' || ec_lname,
               gtdesc,
               COALESCE(eden_code || ' - ','') || adesc
          FROM c_manager.ec_contact  grd
               LEFT OUTER JOIN webset.def_guardiantype rel ON rel.gtrefid = grd.gtrefid
               LEFT OUTER JOIN webset.statedef_prim_lang lang ON grd.ec_primary_language_refid = lang.refid
		 WHERE stdrefid = " . io::get('stdrefid') . "
		 ORDER BY UPPER(ec_lname), UPPER(ec_fname)
    ";

    $list->addColumn("Name");
    $list->addColumn("Relation");
    $list->addColumn("Primary Language");

    $list->addURL = CoreUtils::getURL('emer_add.php', array('stdrefid' => io::get('stdrefid')));
    $list->editURL = CoreUtils::getURL('emer_add.php', array('stdrefid' => io::get('stdrefid')));

    $list->deleteTableName = 'c_manager.ec_contact';
    $list->deleteKeyField = 'ec_refid';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();
?>