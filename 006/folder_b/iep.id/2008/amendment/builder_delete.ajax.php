<?php

	Security::init();

	$RefID = io::get('RefID');

	db::execSQL("
		UPDATE webset.std_iep
	       SET iep_status = 'I'
	     WHERE sIEPMRefID IN ($RefID)
	")

?>