<?PHP
	Security::init();

	$refIDs = explode(',', io::post('RefID', true));

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = IDEAAppArea::ID_EC_MAIN;
	$goal_area_id = IDEAAppArea::ID_EC_GOALS;

	foreach ($refIDs as $refID) {
		$stdRefID = DBCopyRecord::factory('webset.std_general', 'refid')
			->key('refid', $refID)
			->set('iepyear', $stdIEPYear)
			->copyRecord()
			->recordID();
		copyChild($ds, $goal_area_id, $refID, $stdRefID);
	}

	function copyChild($ds, $area, $refID, $stdRefID) {
		$stdIEPYear = $ds->safeGet('stdIEPYear');
		$tsRefID = $ds->safeGet('tsRefID');
		$obj_area_id = IDEAAppArea::ID_EC_OBJECTIVES;
		$goal_area_id = IDEAAppArea::ID_EC_GOALS;

		$objRefids = db::execSQL("
			SELECT std.refid
			  FROM webset.std_general std
			 WHERE stdrefid = " . $tsRefID . "
			   AND area_id = " . $area . "
			   AND int01 = " . $refID . "
		")->assocAll();
		if ($objRefids) {
			foreach ($objRefids AS $objRefid) {
				$newObjRefID = DBCopyRecord::factory('webset.std_general', 'refid')
					->key('refid', $objRefid['refid'])
					->set('iepyear', $stdIEPYear)
					->set('int01', $stdRefID)
					->copyRecord()
					->recordID();
				if ($area == $goal_area_id) {
					$goalRecs = db::execSQL("
						SELECT refid
						  FROM webset.std_constructions
						 WHERE stdrefid = " . $tsRefID . "
				           AND constr_id = 155
				           AND other_id = " . $objRefid['refid'] . "
					")->assocAll();
					foreach ($goalRecs as $goalRec) {
						DBCopyRecord::factory('webset.std_constructions', 'refid')
							->key('refid', $goalRec['refid'])
							->set('iepyear', $stdIEPYear)
							->set('other_id', $newObjRefID)
							->copyRecord();
					}
				}
				copyChild($ds, $obj_area_id, $objRefid['refid'], $newObjRefID);
			}
		}
	}

?>
