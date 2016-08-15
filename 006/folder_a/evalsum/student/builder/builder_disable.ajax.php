<?php

	Security::init();

	$RefID = io::post('RefID');

	db::execSQL("
		UPDATE webset.es_std_esarchived
           SET deleted = 'Y',
               lastuser = '". SystemCore::$userUID ."',
               lastupdate = now()
         WHERE esarefid IN ($RefID)
	");

?>

