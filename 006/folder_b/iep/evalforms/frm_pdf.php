<?php
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	if ($RefID > 0) {
		$form = db::execSQL("
            SELECT fdf_content,
                   forms.mfcrefid,
                   mfcfilename,
                   archived
              FROM webset.std_forms forms
                   INNER JOIN webset.statedef_forms state ON forms.mfcrefid = state.mfcrefid
             WHERE smfcrefid = " . $RefID . "
        ")->assoc();

		$pdf_content = file_get_contents(SystemCore::$physicalRoot . '/applications/webset/iep/evalforms/docs/' . $form['mfcfilename']);

		IDEAFormPDF::factory($pdf_content)
			->setArchived($form['archived'] == 'Y')
			->setSaveFile(SystemCore::$virtualRoot . '/apps/idea/iep/evalforms/frm_pdf_save.php')
			->mergeFDF(base64_decode($form['fdf_content']))
			->show();
	} else {
		$form = db::execSQL("
            SELECT mfcfilename,
                   file_defaults
              FROM webset.statedef_forms state
             WHERE mfcrefid = " . io::geti('mfcrefid') . "
        ")->assoc();

		$pdf_content = file_get_contents(SystemCore::$physicalRoot . '/applications/webset/iep/evalforms/docs/' . $form['mfcfilename']);

		$fdf_content = IDEAFormDefaults::factory($tsRefID)
			->addValues(array('strUrlEnd' => '?tsRefID=' . $tsRefID . '&mfcrefid=' . io::geti('mfcrefid')))
			->addValuesByInclude(str_replace('/applications/webset', '/apps/idea', $form['file_defaults']), array('tsRefID' => $tsRefID))
			->getFDF();

		IDEAFormPDF::factory($pdf_content)
			->setSaveFile(SystemCore::$virtualRoot . '/apps/idea/iep/evalforms/frm_pdf_save.php')
			->mergeFDF($fdf_content)
			->show();
	}
?>