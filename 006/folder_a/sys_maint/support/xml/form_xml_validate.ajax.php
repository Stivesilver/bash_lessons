<?php
	Security::init();
	$xml = IDEADocument::factory()
		->setSource($_POST['xml'])
		->getSourceValidated();

	$xml = preg_replace('/^.+\n/', '', $xml);
	print $xml;
?>
