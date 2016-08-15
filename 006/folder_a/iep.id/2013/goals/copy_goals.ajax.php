<?PHP
	Security::init();

	$refIDs = explode(',', io::post('RefID', true));

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = IDEAAppArea::ID_SEC_GOALS;
	$obj_area_id = IDEAAppArea::ID_SEC_OBJECTIVES;


	foreach ($refIDs as $refID) {
		$stdRefID = DBCopyRecord::factory('webset.std_general', 'refid')
			->key('refid', $refID)
			->set('iepyear', $stdIEPYear)
			->copyRecord()
			->recordID();
		$objRefids = db::execSQL("
			SELECT std.refid
			  FROM webset.std_general std
			 WHERE stdrefid = " . $tsRefID . "
			   AND area_id = " . $obj_area_id . "
			   AND int01 = " . $refID . "
		")->assocAll();
		if ($objRefids) {
			foreach ($objRefids AS $objRefid) {
				DBCopyRecord::factory('webset.std_general', 'refid')
					->key('refid', $objRefid['refid'])
					->set('iepyear', $stdIEPYear)
					->set('int01', $stdRefID)
					->copyRecord();
			}
		}
	}
?>
