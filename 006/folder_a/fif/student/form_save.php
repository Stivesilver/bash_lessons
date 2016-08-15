<?php
	function saveXml($RefID, &$data, $info) {

		$form = IDEAForm::factory($RefID);
		$hisrefid = $form->getParameter('hisrefid');
		$sfrefid = $form->getParameter('sfrefid');
		$values = IDEAForm::factory()->collectValues($_POST);
		$form->setValues($values);
		$form->setUrlCancel('javascript: api.window.dispatchEvent("form_saved");api.window.destroy();');

		$sfrefid = DBImportRecord::factory('webset.std_fif_forms', 'sfrefid')
			->key('sfrefid', $sfrefid)
			->set('hisrefid', $hisrefid)
			->set('frefid', $form->getParameter('frefid'))
			->set('values_content', base64_encode($values))
			->set('lastuser', db::escape(SystemCore::$userUID))
			->set('lastupdate', 'NOW()', true)
			->import()
			->recordID();

		$form->setParameter('sfrefid', $sfrefid);

		io::js('
	        EditClass.get().saveComplete(' . json_encode($RefID) . ');
	    ');
	}
?>
