<?php

	Security::init();

	$refId = io::post('RefID');
	$arrId = explode(',', $refId);

	foreach ($arrId AS $item) {
		DBImportRecord::factory('webset.es_std_evalproc_forms', 'frefid')
			->key('frefid', $item)
			->set('evalproc_id', 'deleted_id', true)
			->set('deleted_id', null)
			->import(DBImportRecord::UPDATE_ONLY);
	}

?>
