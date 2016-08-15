<?php

	Security::init();
	
	define('DOC_ID', 79);
	$doc = IDEADocumentType::factory(DOC_ID);

	if (io::geti('type') == 2) {
		$blocks= $doc->getBlocks();
		$tsRefID = io::geti('tsRefID');
		$iepYear = io::get('iepyear');
		if ($iepYear <= 0) {
			SystemCore::redirect(CoreUtils::getVirtualPath("/applications/webset/iep/iep_year/iep_not_created.php?tsRefID=" . $tsRefID));
		}
	} elseif (io::geti('type') == 3) {
		$dskey = io::get('dskey');
		$ds = DataStorage::factory($dskey, true);
		$tsRefID = $ds->safeGet('tsRefID');
		$blocks= $doc->getBlocks();
	} else {
		$dskey = io::get('dskey');
		$ds = DataStorage::factory($dskey, true);
		$tsRefID = $ds->safeGet('tsRefID');
		$blocks= $doc->getBlocks(io::get('block_id'));
	}


	//	$constr = io::get('constr');
	//	se($constr);
	io::progress(0, 'loading...', true);
	# builder for IEP doc
	$block = IDEABlockBuilder::create(DOC_ID); 
	$block->setSelectedBlocks($blocks);
	$block->setRcDoc(RCPageFormat::LANDSCAPE);
	$block->setStd($tsRefID);
	$block->addBlocks(true);
	$path = $block->compile();
	if (io::geti('type') == 2) {
		SystemCore::redirect(CoreUtils::getVirtualPath(CryptClass::factory()->decode($path)));
	} else {
		io::ajax('nameFile', $path);
		$path = CryptClass::factory()->decode($path);
		io::download($path);
	}
?>
