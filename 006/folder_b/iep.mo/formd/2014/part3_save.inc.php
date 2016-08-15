<?php

	function savePart($RefID, &$data, $info) {
		$dskey = $info['dskey'];
		$ds = DataStorage::factory($dskey);
		$tsRefID = $ds->safeGet('tsRefID');
		$stdIEPYear = $ds->safeGet('stdIEPYear');
		$accs = db::execSQL("
			SELECT sta.actrefid,
				   sta.actname,
				   sta.actcat,
				   sta.actsubcat,
				   sta.other,
				   std.refid AS stdrefid
			  FROM webset.statedef_aa_act_acc AS sta
			 	   LEFT JOIN webset.std_form_d_act AS std ON (sta.actrefid = std.actrefid)
		")->assocAll();
		db::execSQL("
			DELETE FROM webset.std_form_d_act
			 WHERE syrefid = $stdIEPYear
			   AND stdrefid = $tsRefID
		");
		foreach ($accs AS $acc) {
			if (io::exists('check_' . $acc['actrefid'])) {
				if (io::post('check_' . $acc['actrefid']) == 'on') {
						$other = null;
						if ($acc['other'] == 'Y') {
							$other = io::post('oth_' . $acc['actrefid']);
						}
						DBImportRecord::factory('webset.std_form_d_act', 'refid')
							->key('actrefid', $acc['actrefid'])
							->key('syrefid', $stdIEPYear)
							->key('stdrefid', $tsRefID)
							->set('other', $other)
							->setUpdateInformation()
							->import();
				}
			}
		}
	}

?>
