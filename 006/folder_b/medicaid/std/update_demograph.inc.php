<?php

	function updateDemograph($RefID, &$data) {
		$medicaid = $data['msm_medicaid'];
		$stdrefid = $data['stdrefid'];

		DBImportRecord::factory('webset.dmg_studentmst')
			->set('stdmedicatenum', $medicaid)
			->key('stdrefid', $stdrefid);
	}

?>