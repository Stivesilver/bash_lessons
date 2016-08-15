<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$RefIDs = explode(',', io::post('RefID'));
	for ($i = 0; $i < sizeOf($RefIDs); $i++) {
		$mode = substr($RefIDs[$i], 0, 1) == 'O' ? 'O' : 'S';
		$RefID = (int) substr($RefIDs[$i], 1);
		if ($mode == 'S') {
			$SQL = "
                DELETE FROM webset_tx.std_pi
                 WHERE std_refid = " . $tsRefID . "
				   AND iep_year = " . $stdIEPYear . "
                   AND SUBSTRING(mod_sub_id FROM '(.+)_')::int = " . $RefID . "
				   AND accomod_mode = 'S'
            ";
			db::execSQL($SQL);

			$SQL = "
				DELETE FROM webset_tx.std_pi_own
				 WHERE stdrefid = " . $tsRefID . "
				   AND iepyear = " . $stdIEPYear . "
				   AND state_accomodation_id = " . $RefID . "
				   AND category_id IS NULL
			";
			db::execSQL($SQL);
		} else {
			$SQL = "
                DELETE FROM webset_tx.std_pi
                 WHERE std_refid = " . $tsRefID . "
				   AND iep_year = " . $stdIEPYear . "
                   AND SUBSTRING(mod_sub_id FROM '(.+)_')::int = " . $RefID . "
				   AND accomod_mode = 'O'
            ";
			db::execSQL($SQL);

			$SQL = "
				DELETE FROM webset_tx.std_pi_own
				 WHERE refid = " . $RefID . "
			";
			db::execSQL($SQL);
		}
	}
?>
