<?php

	function saveXml($RefID, &$data, $info) {

		$fkey = $RefID;
		$form = IDEAForm::factory($fkey);
		$dsKey = $form->getParameter('dskey');
		$sp_id = $form->getParameter('sp_id');
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
			DBImportRecord::factory('webset.std_forms_xml', 'sfrefid')
				->key('sfrefid', $std_id)
				->set('values_content', base64_encode($values))
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'NOW()', true)
				->import();
		} else {
			$std_id = DBImportRecord::factory('webset.std_forms_xml', 'sfrefid')
				->set('stdrefid', $tsRefID)
				->set('iepyear', $stdIEPYear, true)
				->set('frefid', $state_id)
				->set('values_content', base64_encode($values))
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import()
				->recordID();
			DBImportRecord::factory('webset.std_spconsid', 'sscmrefid')
				->key('sscmrefid', $sp_id)
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
