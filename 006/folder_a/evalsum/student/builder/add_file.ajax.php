<?php

	Security::init();

	CoreUtils::increaseTime();

	define('EVAL_FOLDER', SystemCore::$secDisk . '/Eval');

	$doc_id = io::post('typeBlock');
	$report_date = io::post('report_date');
	$draft = io::post('draft');

	if (io::posti('archive') == 0) {
		io::progress(0, 'loading...', true);

		# builder for IEP doc
		$doc = IDEADocumentType::factory($doc_id);
		$block = IDEABlockBuilder::create($doc_id);
		$block->setSelectedBlocks($doc->getBlocks(io::post('blocks')));
		$block->setRcDoc();
		if ($draft == 'yes') {
			$block->setWaterMark('Draft');
		}
		$block->setStd(io::posti('tsRefID'), null, array('report_date' => $report_date));
		$block->addBlocks(true);
		$path = $block->compile();
		io::ajax('nameFile', $path);
		$path = CryptClass::factory()->decode($path);
		io::download($path);
	} else {
		# save file
		$path = CryptClass::factory()->decode(io::post('nameFile'));
		$name = explode('/temp/', $path);

		if (!SystemCore::$FS->exists(EVAL_FOLDER)) {
			SystemCore::$FS->makeDir(EVAL_FOLDER);
		}

		SystemCore::$FS->rename($path, EVAL_FOLDER . '/' . $name[1]);

		DBImportRecord::factory('webset.es_std_esarchived', 'esarefid')
			->set('stdrefid', io::post('stdrefid'))
			->set('doc_path', $name[1])
			->set('doc_id', io::get('typeBlock'), true)
			->set('lastuser', db::escape(SystemCore::$userUID))
			->set('lastupdate', 'NOW()', true)
			->set('esadate', $report_date)
			->import();

		io::ajax('finish', 1);
	}

?>
