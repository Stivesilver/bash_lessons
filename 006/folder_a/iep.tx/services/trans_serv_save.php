<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'Transportation_chk', io::post('transport'), $stdIEPYear);

	if (io::post('finishFlag') == 'yes') {
		io::js('
            parent.switchTab(5)
        ');
	} else {
		io::js('
            api.reload();
        ');
	}
?>
