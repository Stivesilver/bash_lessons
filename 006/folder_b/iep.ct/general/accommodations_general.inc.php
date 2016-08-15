<?php

	function saveData($RefID, &$data, $params) {

		$dskey = $params['dskey'];
		$ds = DataStorage::factory($dskey);
		$tsRefID = $ds->safeGet('tsRefID');
		$stdIEPYear = $ds->safeGet('stdIEPYear');

		IDEAStudentRegistry::saveStdKey(
			$tsRefID,
			'ct_iep',
			'general_progran_mod',
			io::post('info'),
			$stdIEPYear
		);

	}

?>
