<?php

    Security::init();

    $list = new ListClass();
    $list->title = 'Guardians';

    $list->SQL = "
        SELECT gdrefid,
    		   seqnumber,
	           gdfnm || ' ' || gdlnm,
               gtdesc,
	           CASE WHEN COALESCE(gdeddecision,'Y') = 'Y' THEN 'Yes' ELSE 'No' END						 
		  FROM webset.dmg_guardianmst  grd
               LEFT OUTER JOIN webset.def_guardiantype rel ON rel.gtrefid = grd.gdtype
	     WHERE stdrefid = " . io::get('stdrefid') . "				   
	     ORDER BY seqnumber, gtrank, UPPER(gdlnm), UPPER(gdfnm)
    ";

    $list->addColumn('Order#');
    $list->addColumn('Name');
    $list->addColumn('Relation');
    $list->addColumn('Decision Maker');

    $list->addURL = CoreUtils::getURL('guard_add.php', array('stdrefid' => io::get('stdrefid')));
    $list->editURL = CoreUtils::getURL('guard_add.php', array('stdrefid' => io::get('stdrefid')));

    $list->deleteTableName = 'webset.dmg_guardianmst';
    $list->deleteKeyField = 'gdrefid';

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable($list->deleteTableName)
            ->setKeyField($list->deleteKeyField)
            ->applyListClassMode()
    );

    $list->printList();
?>