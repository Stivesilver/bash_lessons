<?php

	function updateProgmod($RefID, &$data) {
		$modID       = current(FileCSV::factory()->setDataAsString(io::post('stsrefid'))->toArray());
		$freq        = io::post('ssmfreq');
		$begdate     = io::post('ssmbegdate');
		$enddate     = io::post('ssmenddate');
		$implementor = io::post('umrefid');
		$stdrefid    = io::post('stdrefid');
		$malrefid    = io::post('malrefid');

		foreach ($modID as $id) {
			DBImportRecord::factory('webset.std_srv_progmod', 'ssmrefid')
				->set('stsrefid',   $id)
				->set('stdrefid',   $stdrefid)
				->set('ssmbegdate', $begdate)
				->set('ssmenddate', $enddate)
				->set('ssmfreq',    $freq)
				->set('umrefid',    $implementor)
				->set('malrefid',   $malrefid)
				->set('lastuser',   SystemCore::$userUID)
				->set('lastupdate', 'now()', true)
				->import();
		}

	}

?>