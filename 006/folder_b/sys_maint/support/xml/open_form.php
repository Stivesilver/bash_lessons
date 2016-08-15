<?php
	Security::init();

	$key = io::get('key');
	$format = io::get('format');

	$file = IDEAForm::factory($key)
		->getFile($format);

	print_r($file);
?>
