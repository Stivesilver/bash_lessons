<?php

	Security::init();
	if (io::exists('xml')) {
		$xml = io::vpost('xml', DataValidator::factory('string')
			->setHTMLEntitiesPermit(true)
			->setHTMLPermit(true));
	} else {
		$fkey = io::get('fkey');
		$ds = DataStorage::factory($fkey);
		$name = $ds->get('name');
		$xml = $ds->get('xml');
	}

	$res = IDEAFormChecker::factory()
		->setXml($xml)
		->checkError();

	header('Location: ' . CoreUtils::getURL($res));
?>
