<?php
	Security::init();

	$refids = io::get('refids');
	$export_dskey = io::get('export_dskey');
	$ds = DataStorage::factory($export_dskey);
	$xmlTemplate = $ds->safeGet('xmlTemplate');
	$refids = implode("','", explode(',', $refids));
	$data = IDEAData::factory()->xmlExport(
		$xmlTemplate,
		$refids
	);
	print UILayout::factory()
		->newLine()
		->addObject(
			FFTextArea::factory()
				->value($data)
				->width('100%')
				->css('height', SystemCore::$coreVersion == '1' ? '542px' : '500px')
				->css('font-family', 'Courier')
				->css('font-size', '13px'),
			'100%'
		)->toHTML();
?>
