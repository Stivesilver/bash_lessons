<?php
	Security::init(NO_OUTPUT);
	$value = io::post('value', true);
	$value = CryptClass::factory()->encode($value);
	print $value;
?>
