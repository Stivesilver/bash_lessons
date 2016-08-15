<?php
	Security::init();

	$file = CoreUtils::getPhysicalPath(IDEAFormat::getIniOptions('dese_iep_form_path'));
	$file = FileUtils::copyToTmp($file);
	io::download($file);
?>
