<?php
	Security::init();

	$dskey = io::get('dskey', true);
	$ds = DataStorage::factory($dskey);
	$fds = DataStorage::factory();
	$std_id = io::geti('std_id');
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$state_id = io::geti('stateform');

	if ($std_id > 0) {
		$form = db::execSQL("
			SELECT f.form_title,
			       f.form_xml,
			       s.pdf_cont,
			       s.xml_cont,
			       s.evalforms_id,
			       p.mfcrefid
			  FROM webset.es_std_evalproc_forms AS s
			       INNER JOIN webset.es_disdef_evalforms AS f ON s.evalforms_id = f.efrefid
			       LEFT OUTER JOIN webset.statedef_forms AS p ON f.stateform_id = p.mfcrefid
			 WHERE s.frefid = " . $std_id . "
		")->assoc();
		$state_id = $form['evalforms_id'];
	} else {
		//TEMPLATE DATA
		$form = db::execSQL("
			SELECT f.form_title,
			       f.form_xml,
			       d.values,
			       p.mfcrefid
			  FROM webset.es_disdef_evalforms AS f
			       LEFT OUTER JOIN webset.disdef_defaults AS d ON d.form_id = f.stateform_id AND d.vndrefid = VNDREFID AND area = 'PDF' 
			       LEFT OUTER JOIN webset.statedef_forms AS p ON f.stateform_id = p.mfcrefid
			 WHERE efrefid = " . $state_id . "
		")->assoc();
	}

	if($form['mfcrefid'] > 0) {
		$mode = IDEADocumentFormat::PDF;
	} else {
		$mode = IDEADocumentFormat::XML;
	}

	if($std_id == -1) {
		$std_id = 0;
	}

	$idea_form = IDEAForm::factory();
	if ($mode == IDEADocumentFormat::XML) {
		if ($std_id > 0) {
			$values = base64_decode($form['xml_cont']);
		} else {
			$values = IDEAFormDefaults::factory($tsRefID)
				->getXML();
		}
		$template = base64_decode($form['form_xml']);
	} else {
		if ($std_id > 0) {
			$values = base64_decode($form['pdf_cont']);
		} else {
			$values = IDEAFormDefaults::factory($tsRefID)
				->addValues(
					IDEAFormDefaults::xml2array(
						'<values>' . base64_decode($form['values']) . '</values>',
					'id'
					)
				)->getFDF();
		}
		$form_pdf = IDEAFormTemplatePDF::factory($form['mfcrefid']);
		$template = $form_pdf->getXMLFormTemplate();
		$values = IDEAFormPDF::fdf2xml($values, $template);
		$idea_form->setTemplatePDF($form_pdf->getTemplatePath());
	}

	$url = $idea_form
		->setTitle($form['form_title'])
		->setTemplate($template)
		->setValues($values)
		->setUrlCancel('javascript: api.window.destroy();')
		->setUrlFinish('javascript: api.window.dispatchEvent("form_saved");	api.window.destroy();')
		->setUrlSave(CoreUtils::getPhysicalPath('./form_save.php'))
		->setPopulateButton(false)
		->setParameter('dskey', $dskey)
		->setParameter('state_id', $state_id)
		->setParameter('std_id', $std_id)
		->setParameter('evalproc_id', $evalproc_id)
		->setParameter('mfcrefid', $form['mfcrefid'])
		->setParameter('mode', $mode)
		->getUrlPanel();

	io::js('api.goto("' . $url . '")', true);
?>
