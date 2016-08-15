<?php

	Security::init(NO_OUPUT);

	$dskey = io::post('dskey', TRUE);
	$sp_id = io::posti('sp_id', TRUE);
	$state_id = io::posti('state_id', TRUE);
	$std_id = io::posti('std_id');
	$ds = DataStorage::factory($dskey);
	$fds = DataStorage::factory();

	$tsRefID = $ds->safeGet('tsRefID');

	//TEMPLATE DATA
	$form = db::execSQL("
		SELECT form_name,
			   form_xml,
			   values_content
		  FROM webset.statedef_forms_xml stx
			   LEFT OUTER JOIN webset.std_forms_xml std ON sfrefid = " . $std_id . " AND stdrefid = " . $tsRefID . "
		 WHERE stx.frefid = " . $state_id . "
    ")->assoc();

	$template = base64_decode($form['form_xml']);
	if ($std_id > 0) {
		$values = base64_decode($form['values_content']);
	} else {
		$values = IDEAFormDefaults::factory($tsRefID)->getXML();
	}

	$url = IDEAForm::factory()
			->setTitle($form['form_name'])
			->setTemplate($template)
			->setValues($values)
			->setUrlCancel('javascript:api.window.destroy();')
			->setUrlSave(CoreUtils::getPhysicalPath('srv_spconsid_form_save.php'))
			->setUrlFinish(CoreUtils::getURL('srv_spconsid_form_save.php'))
			->setParameter('dskey', $dskey)
			->setParameter('sp_id', $sp_id)
			->setParameter('state_id', $state_id)
			->setParameter('std_id', $std_id)
			->getUrlPanel();

	io::ajax('caption', $form['form_name']);
	io::ajax('url', $url);
?>
