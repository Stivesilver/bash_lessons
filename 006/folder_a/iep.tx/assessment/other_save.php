<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$options = db::execSQL("
		SELECT refid,
			   validvalue,
			   validvalueid
		  FROM webset.glb_validvalues
		 WHERE valuename = 'TX_SAM_Oth'
		 ORDER BY valuename, sequence_number, validvalue ASC
	")->assocAll();
	$items = array();
	$others = array();
	for ($i = 0; $i < count($options); $i++) {
		if (io::post($options[$i]['refid']) == 'Y') {
			$items[] = $options[$i]['refid'];
		}
		if (io::post('oth_' . $options[$i]['refid']) != '') {
			$others[] = 'oth_' . $options[$i]['refid'] . '::' . io::post('oth_' . $options[$i]['refid']);
		}
	}
	if (count($others) > 0) {
		$others[] = '';
	}

	DBImportRecord::factory('webset_tx.std_sam_other', 'refid')
		->key('stdrefid', io::post('stdrefid'))
		->key('iepyear', io::post('iepyear'))
		->set('item', implode(',', $items))
		->set('item_addition', implode('|', $others))
		->set('item_other', io::post('item_other'))
		->set('lastuser', SystemCore::$userUID)
		->set('lastupdate', 'NOW()', true)
		->import();

	if (io::post('finishFlag') == 'no') {
		header('Location: ' . CoreUtils::getURL('other.php', array('dskey' => $dskey)));
	} else {
		io::js('parent.switchTab(4)');
	}
?>
