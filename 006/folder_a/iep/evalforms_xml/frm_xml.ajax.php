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

	if (!$cancel_url) {
		$cancel_url = 'javascript:api.window.destroy();';
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
	}
	if($std_id == -1) {
		$std_id = 0;
	}

	if ($std_id > 0) {
		$values = IDEAStudentFormXML::factory($std_id)->getValues();
	} else {
		/** @var IDEAFormDefaults $get_xml_class_name */
		$get_xml_class_name = $form_state->getClassDefaults();
		if (class_exists($get_xml_class_name) === false) {
			$get_xml_class_name = 'IDEAFormDefaults';
		}
		$obj = new $get_xml_class_name($tsRefID);
		$values = $obj->getXML();
	}

	$url = IDEAForm::factory()
		->setTitle($form_state->getTitle())
		->setTemplate($form_state->getTemplate())
		->setValues($values)
		->setUrlCancel($cancel_url)
		->setUrlSave(CoreUtils::getPhysicalPath('./frm_save.php'))
		->setUrlFinish($finish_url)
		->setPopulateButton(false)
		->setParameter('dskey', $dskey)
		->setParameter('state_id', $state_id)
		->setParameter('std_id', $std_id)
		->getUrlPanel();

	io::js('api.goto("' . $url . '")', true);
?>
