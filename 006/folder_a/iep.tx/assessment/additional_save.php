<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'assessment_additional_needed', io::post('need'), $stdIEPYear);
	IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', 'assessment_additional_evaluation', io::post('evaltype'), $stdIEPYear);

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
