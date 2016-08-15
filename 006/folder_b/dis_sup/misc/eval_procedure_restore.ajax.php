<?php

	Security::init();

	$refId = io::post('RefID');
	$arrId = explode(',', $refId);

	foreach ($arrId AS $item) {
		DBImportRecord::factory('webset.es_std_scr', 'shsdrefid')
			->key('shsdrefid', $item)
			->set('eprefid', 'deleted_id', true)
			->set('deleted_id', null)
			->import(DBImportRecord::UPDATE_ONLY);
	}

?>
