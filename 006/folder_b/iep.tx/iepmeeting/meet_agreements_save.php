<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area = io::get('area');

	$answer = '';
	for ($i = 0; $i < 5; $i++) {
		if (io::post('field' . $i)) {
			$answer .= 'field' . $i . '|' . io::post('field' . $i) . '!!!';
		}
	}
	IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'signatures_agreements_' . $area, $answer, $stdIEPYear);

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
