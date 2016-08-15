<?php

	Security::init();

	if (io::geti('type') == 2) {
		$blocks= IDEABlockBuilder::create(IDEABlockBuilder::TN)->getBlocks();
		$blocksKey = array_keys($blocks);
		$blockId = implode(',', $blocksKey);
		$tsRefID = io::geti('tsRefID');
		$ieptype = IDEABlockBuilder::TN;
		$iepYear = io::get('iepyear');
		if ($iepYear <= 0) {
			SystemCore::redirect(CoreUtils::getVirtualPath("/applications/webset/iep/iep_year/iep_not_created.php?tsRefID=" . $tsRefID));
		}
	} elseif (io::geti('type') == 3) {
		$dskey = io::get('dskey');
		$ds = DataStorage::factory($dskey, true);
		$tsRefID = $ds->safeGet('tsRefID');
		$ieptype = IDEABlockBuilder::TN;
		$blocks= IDEABlockBuilder::create(IDEABlockBuilder::TN)->getBlocks();
		$blocksKey = array_keys($blocks);
		$blockId = implode(',', $blocksKey);
	} else {
		$dskey = io::get('dskey');
		$ds = DataStorage::factory($dskey, true);
		$tsRefID = $ds->safeGet('tsRefID');
		$blockId = io::get('block_id');
		$blockInfo = IDEAFormat::getBlock($blockId);
		$ieptype = $blockInfo['ieptype'];
	}


	//	$constr = io::get('constr');
	//	se($constr);
	io::progress(0, 'loading...', true);
	# builder for IEP doc
	$block = IDEABlockBuilder::create($ieptype);

	$block->setSelectedBlocks($blockId);
	$block->setRcDoc(RCPageFormat::LANDSCAPE);
	$block->setStd($tsRefID);
	$block->addBlocks(true);

	if (io::geti('type') == 2) {
		SystemCore::redirect(CoreUtils::getVirtualPath(CryptClass::factory()->decode($block->compile())));
	} else {
		io::ajax('nameFile', $block->compile());
		$block->getRCDoc()->open();
	}
?>
