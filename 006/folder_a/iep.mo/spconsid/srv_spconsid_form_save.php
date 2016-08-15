<?php

	function saveXml($RefID, &$data, $info) {

		$fkey = io::get('RefID');
		$form = IDEAForm::factory($fkey);
		$dskey = $form->getParameter('dskey');
		$sp_id = $form->getParameter('sp_id');
		$smfcfilename = $form->getParameter('smfcfilename');
		$mfcrefid = $form->getParameter('mfcrefid');
		$pdf_refid = $form->getParameter('pdf_refid');
		$ds = DataStorage::factory($dskey);
		$tsRefID = $ds->safeGet('tsRefID');
		$stdIEPYear = $ds->safeGet('stdIEPYear');

		$values_arr = array();
		$values = "<values>" . PHP_EOL;
		foreach ($_POST as $key => $val) {
			if ($val != '' and substr($key, 0, 5) == 'form_') {
				$values .= '<value name="' . substr($key, 5, strlen($key)) . '">' . stripslashes($val) . '</value>' . PHP_EOL;
				$values_arr[substr($key, 5, strlen($key))] = stripslashes($val);
			}
		}
		$values .= "</values>" . PHP_EOL;

		foreach ($_POST as $key => $val) {
			if ($val != '' and substr($key, 0, 5) == 'form_') {
			}
		}

		$fdf_content = base64_encode(IDEAFormPDF::fdf_prepare($values_arr, $smfcfilename, $mfcrefid));

		if ($pdf_refid > 0) {
			DBImportRecord::factory('webset.std_forms', 'smfcrefid')
				->key('smfcrefid', $pdf_refid)
				->set('smfcdate', 'NOW()', true)
				->set('fdf_content', $fdf_content)
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import();
		} else {
			$pdf_refid = DBImportRecord::factory('webset.std_forms', 'smfcrefid')
				->set('stdrefid', $tsRefID)
				->set('smfcdate', 'NOW()', true)
				->set('iepyear', $stdIEPYear, true)
				->set('mfcrefid', $mfcrefid)
				->set('smfcfilename', $smfcfilename)
				->set('fdf_content', $fdf_content)
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import()
				->recordID();
			DBImportRecord::factory('webset.std_spconsid', 'sscmrefid')
				->key('sscmrefid', $sp_id)
				->set('pdf_refid', $pdf_refid)
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import();
		}

		$form->setValues($values);
		$form->setParameter('pdf_refid', $pdf_refid);

		if (io::post('finishFlag') == 'yes') {
			io::js('api.window.destroy()');
		}
	}
?>
