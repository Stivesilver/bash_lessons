<?php

	Security::init();
  
    $refId = io::post('RefID');
    $arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
		UPDATE webset.std_iep
	       SET iep_status = NULL
	     WHERE sIEPMRefID IN (" . implode(',', array_map('intval', $arrId)) . ")
		";

		db::execSQL($sql);
	}

?>
