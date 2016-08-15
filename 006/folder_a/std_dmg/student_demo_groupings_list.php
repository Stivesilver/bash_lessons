<?php
	
	Security::init(PHP_NOTICE_ON);
	
	$list = new ListClass('list1');
	
	$list->title = 'Groupings';
	
	$list->SQL = "SELECT sgdrefid, sdgname || COALESCE(' (' || sdgdesc || ')', '')
        				FROM webset.dmg_studentgroupingdtl
                        		INNER JOIN webset.disdef_stddemogrouping ON webset.disdef_stddemogrouping.sdgrefid = webset.dmg_studentgroupingdtl.sdgrefid
					   WHERE stdrefid = " . $_GET["stdRefID"] . "
                         AND " . $_GET["stdRefID"] . " != 0
                       ORDER BY sdgdesc";
	
	$list->addColumn("Grouping");    
	$list->printList();
?>
