<?php

	Security::init();
	
	$refId = io::post('RefID');
	$arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
		UPDATE webset.sys_teacherstudentassignment
	       SET stdrefid = stdschoolyear,
               stdschoolyear = NULL
	     WHERE tsrefid IN (" . implode(',', array_map('intval', $arrId)) . ")
		";
		db::execSQL($sql);
		
	}
	
?>
