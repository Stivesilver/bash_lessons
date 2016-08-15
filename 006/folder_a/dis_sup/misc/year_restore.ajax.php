<?php

	Security::init();
  
    $refId = io::post('RefID');
    $arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
			UPDATE webset.std_iep_year
	           SET stdrefid = dsyrefid,
                   lastupdate = now(),
                   lastuser = '" . $_SESSION["s_userUID"] . "'
	         WHERE siymrefid IN (" . implode(',', array_map('intval', $arrId)) . ")
	    ";

		db::execSQL($sql);
	} 
	
?>
