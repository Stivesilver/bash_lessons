<?php

	function saveData($RefID, &$data) {
		$student = new IDEAStudentCT($data['stdrefid']);
		$fields = $student->getTransition();

		foreach ($fields as $key=>$field) {
			$fields[$key] = io::post($key);
		}

		DBImportRecord::factory('webset.std_in_ts', 'tsrefid')
			->key('stdrefid'      ,$data['stdrefid'])
			->key('iepyear'      , $data['iepyear'])
			->set('summary', json_encode($fields))
			->import();

	}

?>