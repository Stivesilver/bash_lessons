<?php

	Security::init(MODE_WS | NO_OUTPUT);
	//
	//	$xmldata = io::vpost(
	//		'xmldata',
	//		DataValidator::factory('string')
	//			->setHTMLEntitiesPermit(true)
	//			->setHTMLPermit(true)
	//	);
	//
	//	$xmlvalues = '<values>' .
	//		io::vpost(
	//			'xmlvalues',
	//			DataValidator::factory('string')
	//				->setHTMLEntitiesPermit(true)
	//				->setHTMLPermit(true)
	//		) . '</values>';

	$xmldata = stripslashes($_POST["xmldata"]);
	$xmlvalues = isset($_POST["xmlvalues"]) ? '<values>' . stripslashes($_POST["xmlvalues"]) . '</values>' : '<values></values>';

	if (substr(strtolower(trim($xmldata)), 0, 4) != "<doc") {
		$xmldata = "<doc>" . $xmldata . "</doc>";
	}

	IDEADocument::factory($xmldata)
		->mergeValues($xmlvalues)
		->output();

?>