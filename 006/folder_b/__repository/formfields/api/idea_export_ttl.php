<?php
	Security::init();

	$export_dskey = io::get('export_dskey');
	$structure_only = io::get('data') != 'yes' ? true : false;
	$alter_table = io::get('alter') == 'yes' ? true : false;
	$ds = DataStorage::factory($export_dskey);
	$table = $ds->safeGet('table');

	$data = IDEAData::getTTL($table, $structure_only, $alter_table);

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
