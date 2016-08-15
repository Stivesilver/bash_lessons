<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs  = new UITabs();

	$tabs->addTab(
		'State General',
		CoreUtils::getURL(
			'./testing_general2.php',
			array(
				'dskey'   => io::get('dskey')
			)
		)
	);

	$tabs->addTab(
		'State Accommodations',
		CoreUtils::getURL(
			'./testing_cmt.php',
			array(
				'dskey'   => io::get('dskey')
			)
		)
	);

	//	$tabs->addTab(
	//		'Old',
	//		CoreUtils::getURL(
	//			'./testing_old.php',
	//			array(
	//				'dskey'   => io::get('dskey')
	//			)
	//		)
	//	);

	echo $tabs->toHTML();

?>
