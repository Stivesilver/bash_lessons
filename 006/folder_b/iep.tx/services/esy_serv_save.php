<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'ESY Services_chk', io::post('esy'), $stdIEPYear);

	if (io::post('finishFlag') == 'yes') {
		io::js('
            parent.switchTab();
        ');
	} else {
		io::js('
            api.reload();
        ');
	}
?>
