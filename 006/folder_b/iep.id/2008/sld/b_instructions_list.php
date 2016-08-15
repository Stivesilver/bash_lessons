<?php

	Security::init();
	
	$list    = new ListClass();
  	$dskey   = io::get('dskey');
	$ds 	 = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID'); 
	$editUrl = CoreUtils::getURL('b_instructions_edit.php', array('dskey' => $dskey));
	$areaID  = 101;
	$list    = new listClass();
	
	$list->addURL  	       = $editUrl;
    $list->editURL 		   = $editUrl;
	$list->title   	       = "Core Instruction Provided";
	$list->deleteKeyField  = "refid";
    $list->deleteTableName = "webset.std_general";
	$list->SQL     		   = "
		SELECT std.refid,
			   order_num,
               validvalue,
               txt01,
               to_char(dat01, 'mm-dd-yyyy'),
               to_char(dat02, 'mm-dd-yyyy'),
               int02,
               txt02,
               txt03,
               order_num
          FROM webset.std_general std
               LEFT OUTER JOIN webset.disdef_validvalues subj ON subj.refid = std.int01
         WHERE stdrefid = " . $tsRefID . "
           AND area_id = $areaID
	     ORDER BY order_num, std.refid
	    ";

    $list->addColumn("Order #");
    $list->addColumn("Academic Area");
    $list->addColumn("Core Instruction");
	$list->addColumn("Begin Date");
	$list->addColumn("End Date");
	$list->addColumn("Total");
    $list->addColumn("Frequency");
    $list->addColumn("Intensity");  
    
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
