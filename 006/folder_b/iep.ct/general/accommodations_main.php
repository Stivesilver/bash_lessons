<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs  = new UITabs();

	$tabs->addTab(
		'Areas',
		CoreUtils::getURL(
			'./accommodations_area_list.php',
			array(
				'dskey'   => io::get('dskey')
			)
		)
	);

	$tabs->addTab(
		'General',
		CoreUtils::getURL(
			'./accommodations_general.php',
			array(
				'dskey'   => io::get('dskey')
			)
		)
	);

	echo $tabs->toHTML();

?>
