<?php

	Security::init();

	$refId = io::post('RefID');
	$arrId = explode(',', $refId);

	foreach ($arrId AS $item) {
		DBImportRecord::factory('webset.std_fif_forms', 'sfrefid')
			->key('sfrefid', $item)
			->set('hisrefid', 'deleted_id', true)
			->set('deleted_id', null)
			->import(DBImportRecord::UPDATE_ONLY);
	}

?>
