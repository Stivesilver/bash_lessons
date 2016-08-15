<?php
	Security::init();

	$text_current = $_POST['text_current'];
	$text_future = $_POST['text_future'];

	$url = IDEADiff::factory()
		->setCurrentVersion($text_future)
		->setPreviousVersion($text_current)
		->getDiffUrl();

	print $url;
?>
