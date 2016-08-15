<?php

	function saveXml($RefID, &$data, $info) {

		$form = IDEAForm::factory($RefID);

		$dskey = $form->getParameter('dskey');
		$smfcrefid = $form->getParameter('smfcrefid');
		$mfcrefid = $form->getParameter('mfcrefid');
		$smfcfilename = $form->getParameter('smfcfilename');
		$save_area = $form->getParameter('save_area');
		$ds = DataStorage::factory($dskey);
		$tsRefID = $ds->safeGet('tsRefID');
		$stdIEPYear = $ds->safeGet('stdIEPYear');

		foreach ($_POST as $key => $val) {
			if ($val != '' and substr($key, 0, 5) == 'form_') $values[substr($key, 5, strlen($key))] = stripslashes($val);
		}

		$fdf = IDEAFormPDF::fdf_prepare($values, io::post('smfcfilename'), $mfcrefid);

		if ($smfcrefid > 0) {
			DBImportRecord::factory('webset.std_forms', 'smfcrefid')
				->key('smfcrefid', $smfcrefid)
				->set('smfcdate', 'NOW()', true)
				->set('fdf_content', base64_encode($fdf))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import();
		} else {
			$smfcrefid = DBImportRecord::factory('webset.std_forms', 'smfcrefid')
				->set('stdrefid', $tsRefID)
				->set('smfcdate', 'NOW()', true)
				->set('iepyear', $stdIEPYear, true)
				->set('mfcrefid', $mfcrefid)
				->set('smfcfilename', $smfcfilename)
				->set('fdf_content', base64_encode($fdf))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import()
				->recordID();
		}

		$form->setParameter('smfcrefid', $smfcrefid);
		$form->setValues(IDEAFormPDF::fdf2xml($fdf, $form->getTemplate()));
		IDEAStudentRegistry::saveStdKey($tsRefID, 'tx_iep', $save_area, $smfcrefid, $stdIEPYear);

		if (io::post('finishFlag') == 'yes') {
				io::js('
		            var edit1 = EditClass.get();
		            edit1.cancelEdit();
	        ');
		} else {
				io::js('
	                api.reload();
	        ');
		}
	}
?>
