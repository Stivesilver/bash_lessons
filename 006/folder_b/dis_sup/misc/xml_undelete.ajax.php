<?php

	Security::init();
	
	$refId = io::post('RefID');
    $arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
			UPDATE webset.std_forms_xml
		                 SET stdrefid = (SELECT iep.stdrefid FROM webset.std_iep_year iep WHERE iepyear = siymrefid),
	                         lastupdate  = now(),
	                         lastuser    = '" . $_SESSION["s_userUID"] . "'
		               WHERE sfrefid IN (" . implode(',', array_map('intval', $arrId)) . ")
		    ";

		db::execSQL($sql);
	} 

?>
