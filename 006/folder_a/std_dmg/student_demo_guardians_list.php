<?php
	
	Security::init(PHP_NOTICE_ON);
	
	$list = new ListClass('list1');
	
	$list->title = 'Guardians';
	
	$list->SQL = "SELECT gdRefID,
                         seqnumber,
                         gdFNm,
	                     gdLNm,
	                     CASE WHEN COALESCE(gdeddecision,'Y') = 'Y' THEN 'Yes' ELSE 'No' END,
	                     CASE WHEN COALESCE(live_with_parent,'Y') = 'Y' THEN 'Yes' ELSE 'No' END,
  						 gdalert_sw
		            FROM webset.dmg_guardianmst
				   WHERE stdrefid = " . $_GET['stdRefID'] . "
				     AND " . $_GET['stdRefID'] . " != 0
				   ORDER BY seqnumber, UPPER(gdLNm), UPPER(gdFNm)
                    ";
	
	$list->addColumn("Order#");
    $list->addColumn("First Name","");
	$list->addColumn("Last Name");
	$list->addColumn("Decision Maker");
	$list->addColumn("Lives With");
	$list->addColumn("Guardian Alert","", "switch");
	
    //$list->editURL = "user_manager_edit.php";
	
	$list->printList();
?>
