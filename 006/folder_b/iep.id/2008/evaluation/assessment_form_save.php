<?php

	function saveXml($RefID, &$data, $info) {

		$fkey = $RefID;
		$form = IDEAForm::factory($fkey);

		$values = "<values>" . PHP_EOL;
		foreach ($_POST as $key => $val) {
			if ($val != '' and substr($key, 0, 5) == 'form_') {
				$values .= '<value name="' . substr($key, 5, strlen($key)) . '">' . stripslashes($val) . '</value>' . PHP_EOL;
			}
		}

		$values .= "</values>" . PHP_EOL;

		# update datastorage
		$form->setValues($values);

		io::js("
			api.window.dispatchEvent('formSaved', {values: " . json_encode(base64_encode($values)) . "});
		");
	}
?>
