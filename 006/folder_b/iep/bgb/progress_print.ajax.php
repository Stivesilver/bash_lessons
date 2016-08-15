<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/uplinkos/classes/pdfClass.v2.0.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$selectedYear = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
	$esy = io::get('esy');

	$block = IDEABlockBuilder::create(IDEABlockBuilder::MO_IEP);
	$block->setRcDoc(RCPageFormat::LANDSCAPE);
	$block->setStd(io::post('tsRefID'), $selectedYear);
	if (io::get('xml') == 'true') {
		$content = '<doc orient="landscape">' . $block->renderProgresReport($esy, true) . '</doc>';
		//io::trace($content);
		$doc = new xmlDoc();
		$doc->edit_mode = "no";
		$doc->xml_data = $content;
		$file_name = $doc->getPdf();
		rename($_SERVER['DOCUMENT_ROOT'] . $file_name, SystemCore::$tempPhysicalRoot . '/' . basename($file_name));
		io::download(SystemCore::$tempPhysicalRoot . '/' . basename($file_name));
	} else {
		$block->renderProgresReport($esy);
		$block->getRCDoc()->open();
	}

?>
