<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);

	$tabs->addTab(
		'Sources',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/05_sociolog_list.php', array(
				'dskey'   => $dskey,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Results and Interpretations',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/results_and_interpretations.php',
			array(
				'dskey'     => $dskey,
				'nexttab'   => 1,
				'top'       => 'yes',
				'key_group' => 'tx_fie',
				'key_name'  => 'sociological_results',
				'lasttab'   => 1
			)
		)
	);

	echo $tabs->toHTML();

	print FFInput::factory()
		->name('screenURL')
		->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))
		->hide()
		->toHTML();

?>