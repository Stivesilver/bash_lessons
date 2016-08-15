<?php

	function saveData($RefID, &$data) {

		$student = new IDEAStudentCT($data['stdrefid']);
		$fields = $student->getTotalSchoolHours();
		foreach ($fields as $key=>$field) {
			$fields[$key] = io::post($key);
		}

		DBImportRecord::factory('webset.std_general', 'refid')
			->key('refid'      , $RefID)
			->set('txt01', json_encode($fields))
			->set('area_id', io::post('area_id'))
			->import();

	}
?>
