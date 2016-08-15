<?php
	Security::init();

	$RefIDs = explode(',', io::post('RefID'));

	for ($i = 0; $i < sizeOf($RefIDs); $i++) {
		if ($RefIDs[$i] > 0) {
			DBImportRecord::factory('webset.sys_proccoordassignment', 'pcsarefid')
				->set('cmrefid', $RefIDs[$i])
				->set('pcrefid', io::geti('pcrefid'))
				->setUpdateInformation()
				->import(DBImportRecord::UPDATE_OR_INSERT);
		}
	}
?>
