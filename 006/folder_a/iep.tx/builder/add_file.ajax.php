<?php

	Security::init();

	if (io::posti('archive') == 0) {
		io::progress(0, 'loading...', true);
		# builder for IEP doc
		$block = IDEABlockBuilder::create(io::post('typeBlock'));

		$block->setSelectedBlocks(io::post('ard_blocks'));
		$block->setStd(io::posti('tsRefID'));
		$block->setHeaderDoc('', 'Date of Meeting');
		$block->addBlocks(true);

		io::ajax('nameFile', $block->compile());
		$block->getRCDoc()->open();
	} else {
		# save file
		$attachments = io::post('ard_blocks');
		$path        = CryptClass::factory()->decode(io::post('nameFile'));
		$name        = explode('/temp/', $path);

		SystemCore::$FS->rename($path, SystemCore::$secDisk . '/Iep/' . $name[1]);

		$pdfCont = readfile(SystemCore::$secDisk . '/Iep/' . $name[1]);
		$pdfCont = base64_encode($pdfCont);

		DBImportRecord::factory('webset.std_iep', 'siepmtrefid')
			->set('pdf_cont',       $pdfCont)
			->set('stdrefid',       io::post('stdrefid'))
			->set('siepmdocfilenm', $name[1])
			->set('form_ids',       $attachments)
			->set('lastuser',       db::escape(SystemCore::$userUID))
			->set('lastupdate',     'NOW()', true)
			->import();

		io::ajax('finish', 1);
	}

?>