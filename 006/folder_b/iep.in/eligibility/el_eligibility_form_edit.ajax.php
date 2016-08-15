<?php

	Security::init(NO_OUPUT);

	$dskey = io::post('dskey', TRUE);
	$state_id = io::posti('state_id', TRUE);
	$std_id = io::posti('std_id');
	$ds = DataStorage::factory($dskey);
	$fds = DataStorage::factory();
	$tsRefID = $ds->safeGet('tsRefID');
	$form_state = IDEAFormTemplateXML::factory($state_id);

	if ($std_id > 0) {
		$values = IDEAStudentFormXML::factory($std_id)->getValues();
	} else {
		$values = IDEAFormDefaults::factory($tsRefID)->getXML();
	}

	$url = IDEAForm::factory()
		->setTitle($form_state->getTitle())
		->setTemplate($form_state->getTemplate())
		->setValues($values)
		->setUrlCancel('javascript:api.window.destroy();')
		->setUrlSave(CoreUtils::getPhysicalPath('el_eligibility_form_save.php'))
		->setUrlFinish(CoreUtils::getURL('el_eligibility_form_save.php'))
		->setParameter('dskey', $dskey)
		->setParameter('state_id', $state_id)
		->setParameter('std_id', $std_id)
		->getUrlPanel();

	io::ajax('caption', $form_state->getTitle());
	io::ajax('url', $url);
?>
