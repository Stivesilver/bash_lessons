<?php

	Security::init(NO_OUPUT);

	$dskey = io::post('dskey', TRUE);
	$ard_id = io::posti('ard_id', TRUE);
	$state_id = io::posti('state_id', TRUE);
	$std_id = io::posti('std_id');
	$ds = DataStorage::factory($dskey);
	$fds = DataStorage::factory();

	$tsRefID = $ds->safeGet('tsRefID');

	//TEMPLATE DATA
	$form = db::execSQL("
		SELECT mfcdoctitle,
			   form_xml,
			   xml_content
		  FROM webset.statedef_forms stf
			   INNER JOIN webset.statedef_forms_xml stx ON stf.xmlform_id = stx.frefid
			   LEFT OUTER JOIN webset.std_forms std ON smfcrefid = " . $std_id . " AND stdrefid = " . $tsRefID . "
		 WHERE stf.mfcrefid = " . $state_id . "
    ")->assoc();

	$template = base64_decode($form['form_xml']);
	if ($std_id > 0) {
		$values = base64_decode($form['xml_content']);
	} else {
		$values = IDEAFormDefaults::factory($tsRefID)->getXML();
	}

	$url = IDEAForm::factory()
			->setTitle($form['mfcdoctitle'])
			->setTemplate($template)
			->setValues($values)
			->setUrlCancel('javascript:api.window.destroy();')
			->setUrlSave(CoreUtils::getPhysicalPath('srv_spconsid_form_save.php'))
			->setUrlFinish(CoreUtils::getURL('srv_spconsid_form_save.php'))
			->setParameter('dskey', $dskey)
			->setParameter('ard_id', $ard_id)
			->setParameter('state_id', $state_id)
			->setParameter('std_id', $std_id)
			->getUrlPanel();

	io::ajax('caption', $form['mfcdoctitle']);
	io::ajax('url', $url);
?>
