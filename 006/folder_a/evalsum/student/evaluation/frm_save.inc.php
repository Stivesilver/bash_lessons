<?php

	function saveXml($RefID, &$data, $info) {

		$fkey = $RefID;
		$form = IDEAForm::factory($fkey);
		$shsdrefid = $form->getParameter('shsdrefid');
		$hsprefid = $form->getParameter('hsprefid');
		$tsrefid = $form->getParameter('stdrefid');
		$title = '';
		if (io::exists('title')) {
			$title = io::post('title');
			$form->updateContolValue('title', $title);
		}
		$values = "<values>" . PHP_EOL;
		foreach ($_POST as $key => $val) {
			if ($val != '' and substr($key, 0, 5) == 'form_') {
				$values .= '<value name="' . substr($key, 5, strlen($key)) . '">' . stripslashes($val) . '</value>' . PHP_EOL;
			}
		}
		$values .= "</values>" . PHP_EOL;

		$new_shsdrefid = DBImportRecord::factory('webset.es_std_scr', 'shsdrefid')
			->key('shsdrefid', $shsdrefid)
			->set('xml_data', base64_encode($values))
			->set('hsprefid', $hsprefid)
			->set('stdrefid', $tsrefid)
			->set('test_name', $title)
			->setUpdateInformation()
			->import(DBImportRecord::UPDATE_OR_INSERT)
			->recordID();

		$form->setValues($values);
		$form->setParameter('shsdrefid', $new_shsdrefid);
	}

?>
