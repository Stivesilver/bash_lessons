<?php

	Security::init();

	$RefID = io::post('RefID');

	db::execSQL("
		UPDATE webset.std_iep
           SET iep_status = 'I',
               lastuser = '". SystemCore::$userUID ."',
               lastupdate = now()
         WHERE siepmrefid IN ($RefID)
	");

?>

