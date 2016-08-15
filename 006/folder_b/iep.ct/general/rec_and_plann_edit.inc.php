<?php

	function saveData($RefID, &$data, $params) {

		$dskey = $params['dskey'];
		$ds = DataStorage::factory($dskey);
		$tsRefID = $ds->safeGet('tsRefID');
		$stdIEPYear = $ds->safeGet('stdIEPYear');

		$recAndPlanning = array(
			'recommendations' => io::post('recommendations'),
			'planning' => io::post('planning'),
			'parent_notif' => io::post('parent_notif'),
			'parent_date' => io::post('parent_date')
		);

		DBImportRecord::factory('webset.std_future_plan', 'fprefid')
			->key('fprefid', $RefID)
			->set('stdrefid', $tsRefID)
			->set('fptext', json_encode($recAndPlanning))
			->set('iepyear', $stdIEPYear)
			->set('lastupdate', io::post('lastupdate'))
			->set('lastuser', io::post('lastuser'))
			->import();
	}

?>
