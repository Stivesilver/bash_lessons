<?php

	function saveXml($RefID, &$data, $info) {

		$fkey = $RefID;
		$form = IDEAForm::factory($fkey);
		$dsKey = $form->getParameter('dskey');
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

		$std_id = IDEAStudentFormXML::factory($std_id)
			->setStdrefid($tsRefID)
			->setIepYear($stdIEPYear)
			->setStateFormId($state_id)
			->setValues($values)
			->saveForm()
			->getFormId();

		$form->setValues($values);
		$form->setParameter('std_id', $std_id);

		if (io::post('finishFlag') == 'yes') {
			io::js('api.window.destroy()');
		}
	}

?>
