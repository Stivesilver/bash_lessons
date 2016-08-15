<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'assistive_technology', io::post('assistive'), $stdIEPYear);

	if (io::post('finishFlag') == 'yes') {
		io::js('
            var edit1 = EditClass.get(); 
            edit1.cancelEdit();
        ');
	} else {
		io::js('
            api.reload();
        ');
	}
?>
