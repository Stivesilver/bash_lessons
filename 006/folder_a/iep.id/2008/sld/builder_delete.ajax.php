<?php

	Security::init();

	$RefID = io::get('RefID');

	db::execSQL("
		UPDATE webset.es_std_esarchived
           SET deleted = 'Y',
               lastupdate  = now(),
               lastuser    = " . SystemCore::$userID . "
         WHERE esarefid IN ($RefID)
	")

?>