<?php

	function saveXml($RefID, &$data, $info) {

		$fkey = $RefID;
		$form = IDEAForm::factory($fkey);
		$dsKey = $form->getParameter('dskey');
		$ard_id = $form->getParameter('ard_id');
		$std_id = $form->getParameter('std_id');
		$state_id = $form->getParameter('state_id');
		$ds = DataStorage::factory($dsKey);
		$tsRefID = $ds->safeGet('tsRefID');
		$stdIEPYear = $ds->safeGet('stdIEPYear');

		$values = "<values>" . PHP_EOL;
		foreach ($_POST as $key => $val) {
			if ($val != '' and substr($key, 0, 5) == 'form_') {
				$values .= '<value name="' . substr($key, 5, strlen($key)) . '">' . stripslashes($val) . '</value>' . PHP_EOL;
			}
		}
		$values .= "</values>" . PHP_EOL;

		if ($std_id > 0) {
			DBImportRecord::factory('webset.std_forms', 'smfcrefid')
				->key('smfcrefid', $std_id)
				->set('smfcdate', 'NOW()', true)
				->set('xml_content', base64_encode($values))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import();
		} else {
			$std_id = DBImportRecord::factory('webset.std_forms', 'smfcrefid')
				->set('stdrefid', $tsRefID)
				->set('smfcdate', 'NOW()', true)
				->set('iepyear', $stdIEPYear, true)
				->set('mfcrefid', $state_id)
				->set('xml_content', base64_encode($values))
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import()
				->recordID();
			DBImportRecord::factory('webset_tx.std_ard', 'sscmrefid')
				->key('sscmrefid', $ard_id)
				->set('formrefid', $std_id)
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import();
		}

		$form->setValues($values);
		$form->setParameter('std_id', $std_id);

		if (io::post('finishFlag') == 'yes') {
			io::js('api.window.destroy()');
		}
	}
?>
