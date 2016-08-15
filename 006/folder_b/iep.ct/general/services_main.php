<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs  = new UITabs();

	$tabs->addTab(
		'Services',
		CoreUtils::getURL(
			'./services_list.php',
			array(
				'dskey'   => io::get('dskey'),
				'esy' => io::get('esy')
			)
		)
	);

	$tabs->addTab(
		'Total School Hours',
		CoreUtils::getURL(
			'./total_school_hours_edit.php',
			array(
				'dskey'   => io::get('dskey')
			)
		)
	);

	echo $tabs->toHTML();

?>
