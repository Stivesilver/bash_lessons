<?php

	Security::init();
	
	$dskey   = io::get('dskey');
	$ds 	 = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID'); 
	$areaID  = 102;
	$editUrl = CoreUtils::getURL('b_intervention_edit.php', array('dskey' => $dskey));
    $list    = new ListClass();
	
	$list->addURL          = $editUrl;
    $list->editURL         = $editUrl;
    $list->deleteKeyField  = "refid";
    $list->deleteTableName = "webset.std_general";
	$list->title 		   = "Intervention Provided";
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
	$list->addColumn("Academic Area of Concern");
	$list->addColumn("Intervention");
	$list->addColumn("Begin Date");
	$list->addColumn("End Date");
	$list->addColumn("Total");
	$list->addColumn("Frequency"); 
	$list->addColumn("Intensity");

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_general')
			->setKeyField('refid')
			->applyListClassMode()
	);
	
	$list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

	$list->printList();
                 
?>
