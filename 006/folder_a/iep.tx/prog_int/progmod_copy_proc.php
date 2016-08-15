<?PHP
	Security::init();

	$refIDs = explode(',', io::post('RefID', true));

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	foreach ($refIDs AS $refid) {
		$refid = explode('_', $refid);
		if ($refid[0] == 'S') {
			$pis = db::execSQL("
				SELECT mod_sub_id,
					   other,
					   accomod_mode
				  FROM webset_tx.std_pi
				 WHERE std_refid = $tsRefID
				   AND iep_year = $refid[2]
				   AND SUBSTRING(mod_sub_id FROM '(.+)_')::int = $refid[1]
			")->assocAll();
			foreach ($pis AS $pi) {
				DBImportRecord::factory('webset_tx.std_pi', 'pi_refid')
					->key('std_refid', $tsRefID)
					->key('iep_year', $stdIEPYear)
					->key('mod_sub_id', $pi['mod_sub_id'])
					->set('std_refid', $tsRefID)
					->set('other', $pi['other'])
					->set('accomod_mode', $pi['accomod_mode'])
					->set('iep_year', $stdIEPYear)
					->setUpdateInformation()
					->import(DBImportRecord::UPDATE_OR_INSERT);
			}
		} elseif ($refid[0] == 'O') {
			$pcats = db::execSQL("
				SELECT refid
				  FROM webset_tx.std_pi_own acc
				  	   INNER JOIN webset_tx.def_pi_modifications_mst cat ON cat.mod_refid = acc.category_id
	         	 WHERE stdrefid = " . $tsRefID . "
			       AND iepyear = " . $refid[2] . "
			       AND state_accomodation_id IS NULL
			       AND refid = $refid[1]
			")->assocAll();
			foreach ($pcats AS $pcat) {
				$nsub = DBCopyRecord::factory('webset_tx.std_pi_own', 'refid')
					->key('refid', $pcat['refid'])
					->set('iepyear', $stdIEPYear)
					->copyRecord()
					->recordID();
				$psubs = db::execSQL("
					 SELECT pi_refid, mod_sub_id
                       FROM webset_tx.std_pi
                      WHERE std_refid = " . $tsRefID . "
					    AND iep_year = " . $refid[2] . "
                        AND accomod_mode = 'O'
                        AND SUBSTRING(mod_sub_id FROM '(.+)_')::INT = " . $pcat['refid'] . "
				")->assocAll();
				foreach ($psubs AS $psub) {
					$msub = explode('_', $psub['mod_sub_id']);
					DBCopyRecord::factory('webset_tx.std_pi', 'pi_refid')
						->key('pi_refid', $psub['pi_refid'])
						->set('iep_year', $stdIEPYear)
						->set('mod_sub_id', $nsub . '_' . $msub[1])
						->copyRecord()
						->recordID();
				}
			}
		}
	}
?>
