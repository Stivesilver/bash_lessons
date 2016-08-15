<?php
	Security::init();
	$path = io::post('path', true);
	$path = CryptClass::factory()->decode($path);
	$path = FileUtils::copyToTmp($path);
	io::download($path);
?>
