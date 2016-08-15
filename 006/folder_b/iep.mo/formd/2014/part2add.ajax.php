<?php

	Security::init();

	$dskey = io::get('dskey');
	$flagedStr = io::get('res');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$flaged = explode(',', $flagedStr);
	$other = json_decode(io::get('other'));

	$sql = "
		SELECT accrefid
          FROM webset.std_form_d_acc std
         WHERE std.stdrefid = $tsRefID
           AND std.syrefid = $stdIEPYear
         ORDER BY accrefid
	";

	$oldflag = db::execSQL($sql)->indexCol();

	// if flaged
	$isFlagged = array_diff($flaged, $oldflag);
	foreach ($isFlagged as $accrefid) {
		DBImportRecord::factory('webset.std_form_d_acc', 'refid')
			->set('stdrefid', $tsRefID)
			->set('syrefid', $stdIEPYear)
			->set('accrefid', $accrefid)
			->setUpdateInformation()
			->import();
	}

	// if unflaged
	$unflaged = implode(',', array_diff($oldflag, $flaged));
	if ($unflaged) {
		$unflagedTie = db::execSQL("
			SELECT refid
			  FROM webset.std_form_d_acc
			 WHERE accrefid IN ($unflaged)
			   AND stdrefid = $tsRefID
	           AND syrefid = $stdIEPYear
		")->indexCol();
		if ($unflagedTie) {
			DBSafeDeleteTie::factory('', 'webset.std_form_d_acc', 'refid')
				->deleteCascade($unflagedTie);
		}
	}

	// edit other description
	if (isset($other)) {
		foreach ($other as $key => $oth) {
			$otherTie = db::execSQL("
				SELECT refid
				  FROM webset.std_form_d_acc
				 WHERE accrefid = $key
				   AND stdrefid = $tsRefID
		           AND syrefid = $stdIEPYear
			")->getOne();
			if ($otherTie) {
				DBImportRecord::factory('webset.std_form_d_acc', 'refid')
					->key('refid' ,$otherTie)
					->set('acc_oth', $oth)
					->setUpdateInformation()
					->import(DBImportRecord::UPDATE_ONLY);
			}
		}
	}
?>

