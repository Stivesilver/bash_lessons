<?php

	function saveXml($RefID, &$data, $info) {

		$form = IDEAForm::factory($RefID);
		$mode = $form->getParameter('mode');
		$state_id = $form->getParameter('state_id');
		$std_id = $form->getParameter('std_id');
		$evalproc_id = $form->getParameter('evalproc_id');
		$mfcrefid = $form->getParameter('mfcrefid');
		$values = IDEAForm::factory()->collectValues($_POST);
		$form->setValues($values);
		$form->setUrlCancel('javascript: api.window.dispatchEvent("form_saved");api.window.destroy();');

		if ($mode == IDEADocumentFormat::XML) {
			$std_id = DBImportRecord::factory('webset.es_std_evalproc_forms', 'frefid')
				->key('frefid', $std_id)
				->set('evalforms_id', $state_id)
				->set('evalproc_id', $evalproc_id)
				->set('xml_cont', base64_encode($values))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import()
				->recordID();
		} else {
			$form_pdf = IDEAFormTemplatePDF::factory($mfcrefid);
			$template = $form_pdf->getXMLFormTemplate();
			$values = IDEAFormDefaults::xml2array($values);
			$values['strUrlEnd'] = '?tsRefID=-1&frefid=' . $std_id . '&area=estracking&proc_id=' . $evalproc_id. '&mfcrefid=' . $mfcrefid;
			$values = IDEAFormPDF::fdf_prepare($values, null, $mfcrefid); 
			$std_id = DBImportRecord::factory('webset.es_std_evalproc_forms', 'frefid')
				->key('frefid', $std_id)
				->set('evalforms_id', $state_id)
				->set('evalproc_id', $evalproc_id)
				->set('pdf_cont', base64_encode($values))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import()
				->recordID();
		}

		$form->setParameter('std_id', $std_id);

		io::js('
            EditClass.get().saveComplete(' . json_encode($RefID) . ');
        ');
	}
?>
