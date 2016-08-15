<?php

	function saveXml($RefID, &$data, $info) {

		$form = IDEAForm::factory($RefID);
		$state_id = $form->getParameter('state_id');
		$refid = $form->getParameter('refid');
		$values = IDEAForm::factory()->collectValues($_POST);
		$form->setValues($values);
		$form->setUrlCancel('javascript: api.window.dispatchEvent("form_saved");api.window.destroy();');

		#Prepare values for unusual format
		$values = str_replace('<values>', '', $values);
		$values = str_replace('</values>', '', $values);
		$values = str_replace('<value name="', '<value id="', $values);
		$refid = DBImportRecord::factory('webset.disdef_defaults', 'refid')
			->key('refid', $refid)
			->set('vndrefid', SystemCore::$VndRefID)
			->set('form_id', $state_id)
			->set('values', base64_encode($values))
			->set('area', 'PDF')
			->set('lastuser', SystemCore::$userUID)
			->set('lastupdate', 'NOW()', true)
			->import()
			->recordID();

		$form->setParameter('refid', $refid);

		io::js('
            EditClass.get().saveComplete(' . json_encode($RefID) . ');
        ');
	}
?>
