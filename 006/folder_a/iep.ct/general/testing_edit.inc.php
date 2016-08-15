<?php

	function saveData($RefID, &$data, $mode) {

		$student = new IDEAStudentCT($mode['stdrefid']);
		$fields = $student->getTesting();

		foreach ($fields as $key => $field) {
			$fields[$key] = io::post($key);
		}
		if ($mode['mode'] == 1) {
			$setkey = 'sswanarr';
		} elseif ($mode['mode'] == 2) {
			$setkey = 'stateansw';
		}
		DBImportRecord::factory('webset.std_assess_state', 'sswarefid')
			->key('stdrefid', $mode['stdrefid'])
			->key('iepyear', $mode['iepyear'])
			->set($setkey, json_encode($fields))
			->setUpdateInformation()
			->import(DBImportRecord::UPDATE_OR_INSERT);

	}

?>
