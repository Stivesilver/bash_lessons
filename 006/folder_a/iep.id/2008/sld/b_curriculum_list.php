<?php
  	
	Security::init();
	
	$list    	= new ListClass();
  	$dskey   	= io::get('dskey');
	$ds 	 	= DataStorage::factory($dskey, true);
	$tsRefID 	= $ds->safeGet('tsRefID'); 
	$editUrl    = CoreUtils::getURL('b_curriculum_edit.php', array('dskey' => $dskey));
	$areaID     = 100;
	$list       = new listClass();

	$list->addURL 		   = $editUrl;
    $list->editURL 		   = $editUrl;
	$list->title    	   = "Data that establishes that the core curriculum is effective for most students";
	$list->deleteKeyField  = "refid";
    $list->deleteTableName = "webset.std_general";
	$list->SQL      	   = "
		SELECT refid,
	           txt01,
               aaadesc,
               to_char(dat01, 'mm-dd-yyyy') AS date01,
               txt02,
               txt03,
               txt04,
               txt05,
               order_num
          FROM webset.std_general std
               INNER JOIN webset.statedef_assess_acc subj ON subj.aaarefid = std.int01
         WHERE stdrefid = " . $tsRefID . "
           AND area_id = $areaID
	     ORDER BY order_num, refid
	   ";

    $list->addColumn("Order #")
    	 ->sqlField('order_num');
    	 
    $list->addColumn("Assessment")
    	 ->sqlField('txt01');
    	 
    $list->addColumn("Area Assessed")
    	 ->sqlField('aaadesc');
    	 
	$list->addColumn("Date")
		 ->sqlField('date01');
		 
    $list->addColumn("Performance Benchmark")
    	 ->sqlField('txt02');
    	 
    $list->addColumn("Grade Level Peers")
    	 ->sqlField('txt03');
    	 
    $list->addColumn("Disaggregated")
    	 ->sqlField('txt04');
    	 
    $list->addColumn("Target")
    	 ->sqlField('txt05');
    
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