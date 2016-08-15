<?php

	function preSave($RefID, &$data, $params) {
		$other = io::get('other_desc');
		IDEAStudentRegistry::saveStdKey($params['tsrefid'], 'mo_iep', 'frequency_progress_reporting_other', $other);
	}

?>
