<?php
	Security::init();

	$ini = IDEAFormat::getIniOptions();
	$file = CoreUtils::getPhysicalPath($ini['in_guardianship_form_file']);
	$file = FileUtils::copyToTmp($file);
	io::download($file);
?>
