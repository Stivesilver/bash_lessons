<?php
	Security::init();

	$ini = IDEAFormat::getIniOptions();
	$file = CoreUtils::getPhysicalPath(io::get('file'));
	$file = FileUtils::copyToTmp($file);
	io::download($file);
?>
