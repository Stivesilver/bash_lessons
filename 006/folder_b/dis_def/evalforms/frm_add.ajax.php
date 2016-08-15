<?php
	Security::init();

	$refid = io::geti('refid');
	$state_id = io::geti('state_id');
	$values = '';

	if ($refid > 0) {
		$form = db::execSQL("
			SELECT values
			  FROM webset.disdef_defaults AS s
			 WHERE s.refid = " . $refid . "
		")->assoc();
		$values = '<values>' . base64_decode($form['values']) . '</values>' ;
		$values = str_replace('<value id="', '<value name="', $values);
	} 

	$form_pdf = IDEAFormTemplatePDF::factory($state_id);
	$template = $form_pdf->getXMLFormTemplate();
	$idea_form = IDEAForm::factory()
		->setTemplatePDF($form_pdf->getTemplatePath());

	$url = $idea_form
		->setTitle($form_pdf->getTitle())
		->setTemplate($template)
		->setValues($values)
		->setUrlCancel('javascript: api.window.destroy();')
		->setUrlFinish('javascript: api.window.dispatchEvent("form_saved");	api.window.destroy();')
		->setUrlSave(CoreUtils::getPhysicalPath('./frm_save.php'))
		->setPopulateButton(false)
		->setParameter('state_id', $state_id)
		->setParameter('refid', $refid)
		->getUrlPanel();

	io::js('api.goto("' . $url . '")', true);
?>
