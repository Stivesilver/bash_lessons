<?PHP
	Security::init();

	$baselines = json_decode(io::post('baselines'));
	$goals = json_decode(io::post('goals'));
	$benchmarks = json_decode(io::post('benchmarks'));
	$dskey = json_decode(io::post('dskey'));

	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	foreach ($baselines as $baseline) {
		$blinfo = db::execSQL("
			SELECT blksa, blbaseline
			  FROM webset.std_bgb_baseline
			 WHERE blrefid = $baseline
		")->assocAll();
		$nblinfo = db::execSQL("
			SELECT blrefid
			  FROM webset.std_bgb_baseline
			 WHERE blksa = " . $blinfo[0]['blksa'] . "
			   AND blbaseline = '" . db::escape($blinfo[0]['blbaseline']) . "'
			   AND esy = 'Y'
			   AND stdrefid = $tsRefID
			   AND siymrefid = $stdIEPYear
		")->getOne();
		if (!$nblinfo) {
			$newbs = DBCopyRecord::factory('webset.std_bgb_baseline', 'blrefid')
				->key('blrefid', $baseline)
				->set('esy', 'Y')
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->copyRecord()
				->recordID();
		} else {
			$newbs = $nblinfo;
		}

		foreach ($goals as $goal) {
			if ($baseline == $goal->parent) {
				if ($nblinfo) {
					$glinfo = db::execSQL("
						SELECT COALESCE(overridetext,gsentance) AS descr
						  FROM webset.std_bgb_goal
						 WHERE grefid = $goal->id
					")->assocAll();
					$oglinfo = db::execSQL("
						SELECT grefid
						  FROM webset.std_bgb_goal
						 WHERE COALESCE(overridetext,gsentance) = '" . db::escape($glinfo[0]['descr']) . "'
						   AND esy = 'Y'
						   AND blrefid = $nblinfo
					")->getOne();
				} else {
					$oglinfo = '';
				}
				if (!$oglinfo) {
					$newgl = DBCopyRecord::factory('webset.std_bgb_goal', 'grefid')
						->key('grefid', $goal->id)
						->set('esy', 'Y')
						->set('blrefid', $newbs)
						->set('lastuser', SystemCore::$userUID)
						->set('lastupdate', 'NOW()', true)
						->copyRecord()
						->recordID();
				} else {
					$newgl = $oglinfo;
				}
				foreach ($benchmarks as $benchmark) {
					if ($goal->id == $benchmark->parent) {
						if ($oglinfo) {
							$bchinfo = db::execSQL("
						SELECT COALESCE(overridetext,bsentance) AS descr
						  FROM webset.std_bgb_benchmark
						 WHERE brefid = $benchmark->id
					")->assocAll();
							$obchinfo = db::execSQL("
						SELECT brefid
						  FROM webset.std_bgb_benchmark
						 WHERE COALESCE(overridetext,bsentance) = '" . db::escape($bchinfo[0]['descr']) . "'
						   AND esy = 'Y'
						   AND grefid = $oglinfo
					")->getOne();
						} else {
							$obchinfo = '';
						}
						if (!$obchinfo) {
							DBCopyRecord::factory('webset.std_bgb_benchmark', 'brefid')
								->key('brefid', $benchmark->id)
								->set('esy', 'Y')
								->set('grefid', $newgl)
								->set('lastuser', SystemCore::$userUID)
								->set('lastupdate', 'NOW()', true)
								->copyRecord();
						}
					}
				}
			}
		}
	}
	io::ajax('res', 1);
?>
