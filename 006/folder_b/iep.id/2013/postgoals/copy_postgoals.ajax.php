<?PHP
	Security::init();

	$refIDs = explode(',', io::post('RefID', true));

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = IDEAAppArea::ID_SEC_POST_GOALS;


	foreach ($refIDs as $refID) {
		$stdRefID = DBCopyRecord::factory('webset.std_general', 'refid')
			->key('refid', $refID)
			->set('iepyear', $stdIEPYear)
			->copyRecord()
			->recordID();
	}
?>
