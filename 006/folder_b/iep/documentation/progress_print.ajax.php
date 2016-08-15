<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$selectedYear = io::geti('siymrefid') > 0 ? io::geti('siymrefid') : $ds->safeGet('stdIEPYear');
	$esy = io::get('esy');

	$block = IDEABlockBuilder::create(IDEABlockBuilder::MO_IEP);
	$block->setRcDoc(RCPageFormat::LANDSCAPE);
	$block->setStd(io::post('tsRefID'), $selectedYear);

	$block->renderProgresReport($esy);
	$block->getRCDoc()->open();
?>
