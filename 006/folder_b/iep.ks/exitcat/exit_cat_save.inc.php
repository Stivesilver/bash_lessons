<?php

	function saveComment($RefID, &$data) {

		DBImportRecord::factory('webset.std_common', 'stdrefid')			
			->key('stdrefid', $RefID)
			->set('id_medical', io::post("id_medical"))
			->set('lastuser', SystemCore::$userUID)
			->set('lastupdate', 'NOW()', true)
			->import();
	}

?>
