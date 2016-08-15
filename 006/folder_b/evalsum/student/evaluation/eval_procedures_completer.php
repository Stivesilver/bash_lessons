<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$val_data = db::execSQL("
		SELECT std.hsprefid,
		       std.xml_data,
		       std.test_name,
		       ass.xml_test,
		       ass.hspdesc,
		       ass.sumdata_sw
		  FROM webset.es_std_scr std
		       INNER JOIN webset.es_scr_disdef_proc ass ON std.hsprefid = ass.hsprefid
		 WHERE shsdrefid = $RefID
		")->assoc();

	if ($val_data['xml_data'] != '') {
		$xml_values = base64_decode($val_data['xml_data']);
	} else {
		$xml_values = IDEAFormDefaults::factory($tsRefID)->getXML();
	}

	$loker = RecordLocker::factory('webset.es_std_scr', io::geti('RefID'));
	$unlockURN = $loker->getUnlockURN();
	$loker->lock();

	$url = IDEAForm::factory()
		->setTitle($val_data['hspdesc'])
		->setTemplate($val_data['xml_test'])
		->setValues($xml_values)
		->setUrlCancel('javascript:api.window.destroy();')
		->setUrlFinish('javascript:api.window.destroy();')
		->setUrlSave(CoreUtils::getPhysicalPath('./eval_procedures_save.inc.php'))
		->setParameter('shsdrefid', $RefID)
		->addJavaScript('
			PageAPI.singleton().page
				.addEventListener(
				PageEvent.UNLOAD,
				function(e) {
					if (window.Desktop && Desktop.currentInstance) {
						Desktop.currentInstance.makeShadowRequest(' . json_encode($unlockURN) . ');
					}
				}
			);
		');

	if ($val_data['hspdesc'] == 'Other') {
		$url->setControls('Title', 'title', $RefID ? $val_data['test_name'] : '', 'text');
	}

	header('Location: ' . $url->getUrlPanel());
?>
