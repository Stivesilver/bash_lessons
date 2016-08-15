<?php

	function update_cover_page($RefID, &$data, $param) {
		$ds = DataStorage::factory($param['dskey']);
		$tsRefID = $ds->safeGet('tsRefID');
		DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
			->key('tsrefid', $tsRefID)
			->set('stdiepmeetingdt', io::post('stdiepmeetingdt'))
			->import(DBImportRecord::UPDATE_ONLY);
	}

?>

