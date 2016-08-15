<?php
	Security::init();

	$area = io::post('area');
	$dskey = io::post('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	switch ($area) {
		case 'sped':
			DBImportRecord::factory('webset.std_srv_sped', 'stdrefid')
				->key('stdrefid', $tsRefID)
				->set('ssmbegdate', io::post('begdate'))
				->set('ssmenddate', io::post('enddate'))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'now()', true)
				->import(DBImportRecord::UPDATE_ONLY);
			break;
		case 'rel':
			DBImportRecord::factory('webset.std_srv_rel', 'stdrefid')
				->key('stdrefid', $tsRefID)
				->set('ssmbegdate', io::post('begdate'))
				->set('ssmenddate', io::post('enddate'))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'now()', true)
				->import(DBImportRecord::UPDATE_ONLY);
			break;
		case 'supp':
			DBImportRecord::factory('webset.std_srv_sup', 'stdrefid')
				->key('stdrefid', $tsRefID)
				->set('ssmbegdate', io::post('begdate'))
				->set('ssmenddate', io::post('enddate'))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'now()', true)
				->import(DBImportRecord::UPDATE_ONLY);
			break;
		case 'pers':
			DBImportRecord::factory('webset.std_srv_supppersonnel', 'stdrefid')
				->key('stdrefid', $tsRefID)
				->set('sspbegDate', io::post('begdate'))
				->set('sspEndDate', io::post('enddate'))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'now()', true)
				->import(DBImportRecord::UPDATE_ONLY);
			break;
		case 'kspma':
			DBImportRecord::factory('webset.std_srv_progmod', 'stdrefid')
				->key('stdrefid', $tsRefID)
				->set('ssmbegdate', io::post('begdate'))
				->set('ssmenddate', io::post('enddate'))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'now()', true)
				->import(DBImportRecord::UPDATE_ONLY);
			break;
	}
?>
