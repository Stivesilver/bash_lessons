<?php
	function saveXml($RefID, &$data, $info) {

		$form = IDEAForm::factory($RefID);
		$dsKey = $form->getParameter('dskey');
		$std_id = $form->getParameter('std_id');
		$state_id = $form->getParameter('state_id');
		$finish_url = $form->getUrlFinish();
		$ds = DataStorage::factory($dsKey);
		$tsRefID = $ds->safeGet('tsRefID');
		$stdIEPYear = $ds->safeGet('stdIEPYear');
		$values = IDEAForm::collectValues($_POST);

		$std_id = IDEAStudentFormXML::factory($std_id)
			->setStdrefid($tsRefID)
			->setIepYear($stdIEPYear)
			->setStateFormId($state_id)
			->setValues($values)
			->saveForm()
			->getFormId();

		$form->setValues($values);
		$form->setParameter('std_id', $std_id);
	}
?>
