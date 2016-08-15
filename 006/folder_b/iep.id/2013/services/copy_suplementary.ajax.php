<?PHP
	Security::init();

	$refIDs = explode(',', io::post('RefID', true));

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	foreach ($refIDs as $refID) {
		$stdRefID = DBCopyRecord::factory('webset.std_srv_sup', 'ssmrefid')
			->key('ssmrefid', $refID)
			->set('iepyear', $stdIEPYear)
			->copyRecord()
			->recordID();
	}
?>
