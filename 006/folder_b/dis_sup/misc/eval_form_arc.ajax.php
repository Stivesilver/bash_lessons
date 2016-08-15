<?php

	Security::init();
	
	$refId = io::post('RefID');
	$arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
			UPDATE webset.es_std_evalproc_forms
			   SET archived = NULL
			 WHERE frefid IN (" . implode(',', array_map('intval', $arrId)) . ")
		";
		
		db::execSQL($sql);
	}
 
?>
