<?php

	Security::init();

	$xml = $_POST['xml'];

	$key = IDEAForm::factory()
		->setTemplate($xml)
		->getFormDSKey();

	print_r($key);
?>
