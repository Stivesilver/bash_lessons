<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$RefIDs = explode(',', io::post('RefID'));
	for ($i = 0; $i < sizeOf($RefIDs); $i++) {
		if ($RefIDs[$i] != '') {
			db::execSQL("
				DELETE FROM webset_tx.std_lre_statements
				 WHERE stdrefid = " . $tsRefID . "
				   AND iep_year = " . $stdIEPYear . "
				   AND area = '" . $RefIDs[$i] . "'
				
			");
		}
	}
?>