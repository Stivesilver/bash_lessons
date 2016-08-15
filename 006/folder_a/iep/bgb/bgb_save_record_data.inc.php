<?php

	function saveData($RefID, &$data) {
		$recData = array();
		for ($i = 1;;$i++) {
			if (io::exists('id-' . $i)) {
				$id = io::post('id-' . $i);
				$value = io::post('row-' . $i);
				$recData[$id] = $value;
			} else {
				break;
			}
		}
		if (!empty($recData)) {
		DBImportRecord::factory('webset.std_bgb_measure_data')
			->key('mdrefid', $RefID)
			->set('mdata', json_encode($recData))
			->import(DBImportRecord::UPDATE_ONLY);
		}
	}

