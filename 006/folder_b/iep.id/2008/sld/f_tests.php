<?php
  	
  	Security::init();

    $dskey    = io::get('dskey');
	$ds 	  = DataStorage::factory($dskey, true);
	$tsRefID  = $ds->safeGet('tsRefID'); 
	$areaID   = 108;
	$list 	  = new ListClass();
    $editUrl  = CoreUtils::getURL('f_tests_edit.php', array('dskey' => $dskey));
	
	$list->addURL 		   = $editUrl;
    $list->editURL 		   = $editUrl;
    $list->deleteKeyField  = "refid";
    $list->deleteTableName = "webset.std_general";
	$list->title 		   = "Documentation of English Language Proficiency when the Student is an English Learner (EL)";
	$list->SQL 			   = "
		SELECT refid,
		   	   order_num,
               to_char(dat01, 'mm-dd-yyyy'),
               txt01,
               txt02,
               NULL,
               NULL,
               NULL,
               NULL,
               order_num
          FROM webset.std_general std
         WHERE stdrefid = " . $tsRefID . "
           AND area_id = $areaID
	     ORDER BY order_num, refid
	   ";

	$list->addColumn("Order #");
	$list->addColumn("Date");
	$list->addColumn("Assessment/Documentation");
	$list->addColumn("Result/Score");
	
	$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.std_general')
				->setKeyField('refid')
				->applyEditClassMode()
	);
	
	$list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

	$list->printList();
	
    

?>
