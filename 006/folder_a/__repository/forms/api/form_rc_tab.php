<?php
	Security::init(NO_OUTPUT | EPS_OFF);

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
	$xmldata = stripslashes($xml);

	if (substr(strtolower(trim($xmldata)), 0, 4) != "<doc") {
		$xmldata = "<doc>" . $xmldata . "</doc>";
	}

	if (io::get('format') == 'html') {
		try {
			print IDEADocument::factory($xmldata)
				->output(IDEADocumentFormat::HTML);
		} catch (Exception $e) {
			print 'Wrong XML';
		}
	} else {
		try {
			IDEADocument::factory($xmldata)
				->output();
		} catch (Exception $e) {
			print 'Wrong XML';
		}
	}
?>
