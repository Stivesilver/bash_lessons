<?php

	Security::init();

	$dskey = io::get('dskey');
	$mode = io::get('mode');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	if ($mode == 'S') {
		IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'lre_effort_not_success', io::post('reasons'), $stdIEPYear);
		$nextTab = 2;
	} else {
		IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'lre_options_rejected', io::post('rejected'), $stdIEPYear);
		$nextTab = 4;
	}

	if (io::post('finishFlag') == 'yes') {
		io::js('
            parent.switchTab(' . $nextTab . ')
        ');
	} else {
		io::js('
            api.reload();
        ');
	}
?>
