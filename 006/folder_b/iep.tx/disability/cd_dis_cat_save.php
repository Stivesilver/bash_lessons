<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'disability_letter', io::post('answer'));
?>
