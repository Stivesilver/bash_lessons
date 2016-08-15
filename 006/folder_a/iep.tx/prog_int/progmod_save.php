<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	function saveSubjects($mode, $accommodation_id, $subjects, $tsRefID, $stdIEPYear) {
		# Delete old progmod
		$SQL = "
			DELETE FROM webset_tx.std_pi
			 WHERE std_refid = " . $tsRefID . "
			   AND iep_year = " . $stdIEPYear . "
			   AND accomod_mode = '" . $mode . "'
			   AND SUBSTRING(mod_sub_id FROM '(.+)_')::int = " . $accommodation_id . "
		";
		db::execSQL($SQL);

		#Subjects save
		$RefIDs = explode(',', $subjects);

		for ($i = 0; $i < sizeOf($RefIDs); $i++) {
			if ($RefIDs[$i] > 0) {
				DBImportRecord::factory('webset_tx.std_pi', 'pi_refid')
					->set('std_refid', $tsRefID)
					->set('iep_year', $stdIEPYear)
					->set('mod_sub_id', $accommodation_id . '_' . $RefIDs[$i])
					->set('accomod_mode', $mode)
					->set('lastuser', db::escape(SystemCore::$userUID))
					->set('lastupdate', 'NOW()', true)
					->import();
			}
		}
	}

	if (io::post('mode') == 'S') {

		$SQL = "
			DELETE FROM webset_tx.std_pi_own
			 WHERE stdrefid = " . $tsRefID . "
			   AND iepyear = " . $stdIEPYear . "
			   AND state_accomodation_id = " . io::posti('sub_mod_refid') . "
		";
		db::execSQL($SQL);

		#Own ProgMods save
		$progmod = db::execSQL("
			SELECT sub_mod_desc
			  FROM webset_tx.def_pi_modifications_dtl
			 WHERE sub_mod_refid = " . io::posti('sub_mod_refid') . "
		")->getOne();
		if (io::post('subject_own') != '' || io::post('accommodation') != $progmod) {
			DBImportRecord::factory('webset_tx.std_pi_own', 'refid')
				->set('stdrefid', $tsRefID)
				->set('iepyear', $stdIEPYear)
				->set('state_accomodation_id', io::posti('sub_mod_refid'))
				->set('subject_own', io::post('subject_own'))
				->set('accommodation', io::post('accommodation'))
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import();
		}

		saveSubjects('S', io::posti('sub_mod_refid'), io::post('subjects'), $tsRefID, $stdIEPYear);
	} else {
		$ownid = DBImportRecord::factory('webset_tx.std_pi_own', 'refid')
			->key('refid', io::posti('RefID'))
			->set('stdrefid', $tsRefID)
			->set('iepyear', $stdIEPYear)
			->set('category_id', io::posti('category_id'))
			->set('seqnum', io::post('seqnum'))
			->set('accommodation', io::post('own_accommodation'))
			->set('lastuser', db::escape(SystemCore::$userUID))
			->set('lastupdate', 'NOW()', true)
			->import()
			->recordID();

        saveSubjects('O', $ownid, io::post('subjects'), $tsRefID, $stdIEPYear);

	}

	if (io::post('finishFlag') == 'yes') {
		io::js('
            var edit1 = EditClass.get();
            edit1.cancelEdit();
        ');
	} else {
		io::js('
            addNext(' . json_encode($dskey) . ', ' . json_encode(io::get("area")) . ', ' . json_encode(io::post("subjects")) . ', ' . json_encode(io::post('mode')) . ');
        ');
	}
?>
