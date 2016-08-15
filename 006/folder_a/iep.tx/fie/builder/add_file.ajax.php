<?php

	Security::init();

	if (io::post('archive') == 0) {
		# builder for IEP doc
		$block = IDEABlockBuilder::create(IDEABlockBuilder::FIE);
		$block->setStd(io::post('tsRefID'));
		$block->setQueryData($_POST);
		$block->setSelectedBlocks(io::post('fie_blocks'));
		$block->setHeaderDoc('Date of FIE: ', io::post('fie_date'));
		$block->addBlocks(true);

		io::ajax('nameFile', $block->compile());
		$block->getRCDoc()->open();
	} else {
		# save file
		$attachments = io::post('fie_blocks');
		$path        = CryptClass::factory()->decode(io::post('nameFile'));
		$name        = explode('/temp/', $path);

		SystemCore::$FS->rename($path, SystemCore::$secDisk . '/Iep/' . $name[1]);

		$pdfCont = readfile(SystemCore::$secDisk . '/Iep/' . $name[1]);
		$pdfCont = base64_encode($pdfCont);

		if ($attachments != '') {
			$SQL = "UPDATE webset.std_forms
                   SET archived='Y'
                 WHERE smfcrefid in ($attachments)";
			$result = db::execSQL($SQL);
			if (!$result) se($SQL);
		}

		DBImportRecord::factory('webset_tx.std_fie_arc', 'siepmtrefid')
			->set('pdf_cont',       $pdfCont)
			->set('stdrefid',       io::post('stdrefid'))
			->set('siepmdocdate',   io::post('meeting_date'))
			->set('siepmdocfilenm', $name[1])
			->set('form_ids',       $attachments)
			->set('lastuser',       db::escape(SystemCore::$userUID))
			->set('lastupdate',     'NOW()', true)
			->import();

		io::ajax('finish', 1);

	}

?>