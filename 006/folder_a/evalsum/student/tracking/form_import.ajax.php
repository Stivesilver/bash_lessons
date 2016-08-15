<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$RefIDs = explode(',', io::post('RefID'));
	for ($i=0; $i < sizeOf($RefIDs); $i++) {
		if ($RefIDs[$i] > 0) {
			DBImportRecord::factory('webset.es_std_evalproc_forms', 'frefid')
				->set('import_xml_id', $RefIDs[$i])
				->set('evalproc_id', $evalproc_id)
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import();
		}
	}
?>
