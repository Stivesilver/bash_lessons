<?php

	Security::init();

	$RefID = io::get('RefID');

	db::execSQL("
		UPDATE webset_tx.std_fie_arc
           SET iep_status = 'I'
         WHERE sIEPMRefID IN ($RefID)
	");

?>