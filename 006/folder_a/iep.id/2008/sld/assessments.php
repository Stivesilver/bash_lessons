<?php

	Security::init();
	
	$list    	= new ListClass();
  	$dskey   	= io::get('dskey');
	$ds 	 	= DataStorage::factory($dskey, true);
	$tsRefID 	= $ds->safeGet('tsRefID'); 
	$areaID     = io::geti('area_id');
	$editUrl    = CoreUtils::getURL('assessments_edit.php', array('dskey' => $dskey, 'area_id' => $areaID));

	$list->addURL 		   = $editUrl;
    $list->editURL 		   = $editUrl;
	$list->deleteKeyField  = "refid";
    $list->deleteTableName = "webset.std_general";
	$list->title 		   = 'Pattern of Strengths and Weaknesses in Psychological Processing Skills That Impact Learning';
	$list->SQL   		   = "
		SELECT std.refid,
               order_num,
               replace(txt08,'\r\n', '<br>'),
               to_char(dat01, 'mm-dd-yyyy'),
               txt01,
               replace(txt02, '\r\n', '<br>'),
               replace(txt03, '\r\n', '<br>'),
               replace(txt04, '\r\n', '<br>'),
               txt05,
               order_num
          FROM webset.std_general std
         WHERE stdrefid = " . $tsRefID . "
           AND area_id = " . $areaID . "
	     ORDER BY order_num, std.refid
	   ";

	$list->addColumn("Order #");     
	$list->addColumn('Processing Area');
	$list->addColumn("Date");
	$list->addColumn("Name of Assessment");
	$list->addColumn("Subtest(s)");
	$list->addColumn("SS"); 
	$list->addColumn("%ile");
	$list->addColumn("Evaluator/Title");
	
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
