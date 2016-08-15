<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	$values = array_map('intval', explode(',', io::get('values')));

	$SQL = "
		DELETE
		  FROM webset.std_tn_serv_summ_rc
		 WHERE grefid IN (" . implode(',', $values) . ");

		DELETE
		  FROM webset.std_tn_serv_summ_nr
		 WHERE grefid IN (" . implode(',', $values) . ");
	";
	db::execSQL($SQL);
?>