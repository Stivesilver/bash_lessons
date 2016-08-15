<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$diff = $ds->safeGet('diff');

	$finalHTML = UILayout::factory()
		->newLine('[height: 100%; align: center;]')
		->addObject(
			FFTextArea::factory()
				->autoHeight(true)
				->value($diff), 
			'middle'
		);

	print $finalHTML->toHTML();
?>
