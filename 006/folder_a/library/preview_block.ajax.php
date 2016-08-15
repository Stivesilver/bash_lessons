<?php

	Security::init();
	CoreUtils::increaseTime();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID');
	$block_id = io::get('block_id');
	$doc_id = io::get('doc_id');
	$block = IDEABlockBuilder::create($doc_id);
	$block->setSelectedBlocks(IDEADocumentBlock::getBlockByID($block_id));
	$block->setStd($tsRefID);
	$block->addBlocks(true); 
	$path = $block->compile();
	io::ajax('nameFile', $path);
	$path = CryptClass::factory()->decode($path);
	io::download($path);
?>
