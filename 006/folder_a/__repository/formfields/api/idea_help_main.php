<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	if (io::post('constr_id')) {
		$xml = IDEADef::getConstructionTemplate(io::post('constr_id'));
		$doc = new xmlDoc();
		$doc->edit_mode = 'no';
		$doc->xml_data = $xml;
		echo $doc->getHtml();

	} elseif (io::post('html')) {
		echo io::post('html');
	}
?>
