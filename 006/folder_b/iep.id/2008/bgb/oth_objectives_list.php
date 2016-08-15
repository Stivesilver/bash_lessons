<?php

	Security::init();
	
	$list 	 = new ListClass("objective");
	$dskey   = io::get('dskey');
	$editUrl = CoreUtils::getURL(
		'oth_objectives_edit.php', 
		array('dskey' => $dskey, 'grefid' => io::get("grefid"))
	);

    $list->title  		   = "Objectives";
    $list->addURL  		   = $editUrl;
    $list->editURL 	       = $editUrl;
    $list->deleteTableName = "webset.std_oth_objectives";
    $list->deleteKeyField  = "orefid";
    $list->multipleEdit    = "no";
    $list->SQL			   = "
    	SELECT orefid,
               order_num,
               objective_own,
               order_num
          FROM webset.std_oth_objectives o
         WHERE grefid = " . io::get("grefid") . "
         ORDER BY COALESCE(order_num, orefid)
        ";
                  
    if (io::get("ESY") == 'Y') {
    	$list->title .= "ESY ";
    }

	$list->addColumn("Order #",   "5%");
    $list->addColumn("Objective", "95%");

    if (io::get("grefid") != -1) {
        $list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.std_oth_objectives')
				->setKeyField('orefid')
				->applyEditClassMode()
		);
        
    }

    $list->printList();

?>
