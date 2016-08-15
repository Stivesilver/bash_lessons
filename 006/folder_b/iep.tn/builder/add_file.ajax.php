<?php

	Security::init();

	if (io::posti('archive') == 0) {
		io::progress(0, 'loading...', true);

		# builder for IEP doc
		$blocks = io::post('blocks');
		$block_objects = array();
		foreach (explode(',', $blocks) as $id) {
			$block_objects[] = IDEADocumentBlock::getBlockByID($id);
		}
		$block = IDEABlockBuilder::create(io::post('typeBlock'));
		$block->setSelectedBlocks($block_objects);

		$block->setRcDoc(RCPageFormat::LANDSCAPE);
		$tid = io::posti('IEPType');
		$ids = IDEAFormat::getIniOptions('draft_iep_types');
		if (in_array($tid, explode(',', $ids))) {
			$block->setWaterMark('Draft');
		}
		$block->setStd(io::posti('tsRefID'));
		$block->addBlocks(true);
		io::ajax('nameFile', $block->compile());
		$block->getRCDoc()->open();
	} else {
		# save file
		$path = CryptClass::factory()->decode(io::post('nameFile'));
		$name = explode('/temp/', $path);

		SystemCore::$FS->rename($path, SystemCore::$secDisk . '/Iep/' . $name[1]);

		$pdfCont = readfile(SystemCore::$secDisk . '/Iep/' . $name[1]);
		$pdfCont = base64_encode($pdfCont);

		DBImportRecord::factory('webset.std_iep', 'siepmrefid')
			->set('pdf_cont', $pdfCont)
			->set('stdrefid', io::post('stdrefid'))
			->set('siepmtrefid', io::post('IEPType'))
			->set('siepmdocfilenm', $name[1])
			->set('lastuser', db::escape(SystemCore::$userUID))
			->set('lastupdate', 'NOW()', true)
			->import();

		io::ajax('finish', 1);
	}

?>
