<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$f_refid = io::geti('f_refid', true);
	$t_data = io::get('t_refid', true);
	$type = 1;
	if ($t_data) {
		$t_data = explode('_', $t_data);

		$type = $t_data[0];
		$t_refid = $t_data[1];
	}

	if (!$f_refid) {
		$fimport = DBImportRecord::factory('webset.std_forms', 'smfcrefid')
			->set('stdrefid', $tsRefID);
		if ($type == 1) {
			$fimport->set('dfrefid', $t_refid);
		} elseif ($type == 2) {
			$fimport->set('mfcrefid', $t_refid);
		}
		$f_refid = $fimport->set('iepyear', $stdIEPYear)
			->setUpdateInformation()
			->import(DBImportRecord::INSERT_ONLY)
			->recordID();
	}

	echo UIFBIDEAForm::factory($f_refid, $type)
		->toHTML();

?>
