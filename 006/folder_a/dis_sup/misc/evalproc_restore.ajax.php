<?php
  
    Security::init();
  
    $refId = io::post('RefID');
    $arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
		UPDATE webset.es_std_evalproc
	       SET stdrefid = delrefid
	     WHERE eprefid IN (" . implode(',', array_map('intval', $arrId)) . ");
		";

		db::execSQL($sql);
	}
  
?>
