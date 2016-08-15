<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);

	$tabs->addTab(
		'Sources',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/09_sources_list.php', array(
				'dskey'   => $dskey,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Strengths/Weaknesses',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/09_weaknesses_list.php',
			array(
				'dskey'     => $dskey,
				'nexttab'   => 1,
				'top'       => 'yes'
			)
		)
	);

	echo $tabs->toHTML();

	print FFInput::factory()
		->name('screenURL')
		->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))
		->hide()
		->toHTML();