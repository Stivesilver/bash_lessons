<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs  = new UITabs();

	$tabs->addTab(
		'Services',
		CoreUtils::getURL(
			'./tn_services_list.php',
			array(
				'dskey'   => io::get('dskey')
			)
		)
	);

	$tabs->addTab(
		'Justification for Provision',
		CoreUtils::getURL(
			'./tn_services_just_provision_list.php',
			array(
				'dskey'   => io::get('dskey')
			)
		)
	);

	echo $tabs->toHTML();
?>