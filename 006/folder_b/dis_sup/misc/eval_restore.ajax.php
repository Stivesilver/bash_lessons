<?php

	Security::init();
	
	$refId = io::post('RefID');
	$arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
		UPDATE webset.es_std_esarchived
	       SET deleted = NULL
	     WHERE esarefid IN (" . implode(',', array_map('intval', $arrId)) . ")
		";
		
		db::execSQL($sql);
	}
 
?>
