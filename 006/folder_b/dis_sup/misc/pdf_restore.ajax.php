<?php

	Security::init();
	
	$refId = io::post('RefID');
    $arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
			UPDATE webset.std_forms
	           SET archived = NULL
	         WHERE smfcRefID IN (" . implode(',', array_map('intval', $arrId)) . ")
	        ";

		db::execSQL($sql);
	} 

?>
