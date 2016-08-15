<?php

	Security::init();

	$RefIDs = explode(',', io::post('RefID'));
	for ($i = 0; $i < sizeOf($RefIDs); $i++) {
		if ($RefIDs[$i] > 0) {
			DBImportRecord::factory('webset.es_std_evalproc', 'eprefid')
				->key('eprefid', $RefIDs[$i])
				->set('delrefid', 'stdrefid', TRUE)
				->set('stdrefid', NULL)
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', TRUE)
				->import();
		}
	}

?>
