<?php

	Security::init();

	$dskey = io::get('dskey', true);
	$ds = DataStorage::factory($dskey);
	$fds = DataStorage::factory();
	$std_id = io::geti('std_id');
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stdrefid = $ds->safeGet('stdrefid');
	$state_id = io::geti('stateform', true);
	$cancel_url = io::get('cancel_url');
	$finish_url = io::get('finish_url');
	$form_state = IDEAFormTemplateXML::factory($state_id);
	$screenURL = $ds->safeGet('screenURL');

	if (!$cancel_url) {
		$cancel_url = 'javascript:api.reload();';
	} else {
		$cancel_url = CoreUtils::getURL($cancel_url, array('dskey' => $dskey));
	}

	if (!$finish_url) {
		$finish_url = 'javascript:api.window.destroy();';
	} else {
		$finish_url = CoreUtils::getURL($finish_url, array('dskey' => $dskey));
	}

	if (!$std_id) {
		$form_std = IDEAStudentFormXML::factory()
			->setStdrefid($tsRefID)
			->setIepYear($stdIEPYear)
			->setStateFormId($state_id)
			->searchForm();
		$std_id = $form_std->getFormId();
		$finish_url = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	}
	if($std_id == -1) {
		$std_id = 0;
	}

	if ($std_id > 0) {
		$values = IDEAStudentFormXML::factory($std_id)->getValues();
	} else {
		$values = IDEAFormDefaults::factory($tsRefID)->getXML();
	}



	$url = IDEAForm::factory()
		->setTitle($form_state->getTitle())
		->setTemplate($form_state->getTemplate())
		->setValues($values)
		->setUrlCancel($cancel_url)
		->setUrlSave(CoreUtils::getPhysicalPath('./xml_form_save.php'))
		->setUrlFinish($finish_url)
		->setPopulateButton(true)
		->setParameter('dskey', $dskey)
		->setParameter('state_id', $state_id)
		->setParameter('std_id', $std_id)
		->getUrlPanel();

	io::js('api.goto("' . $url . '")', true);
?>
