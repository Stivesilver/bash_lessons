<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(5);

	$tabs->addTab(
		'Sources',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/08_sources_list.php', array(
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
				'key_name'  => 'adaptice_b',
				'lasttab'   => 0
			)
		)
	);

	$tabs->addTab(
		'Functioning',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/08_functioning_edit.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => 2,
				'top'     => 'yes',
			)
		),
		'functioning'
	);

	$tabs->addTab(
		'Behavior',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/08_behavior_list.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => 3,
				'top'     => 'yes',
			)
		)
	);

	$tabs->addTab(
		'Composite Score',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/08_composite_edit.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => '-1',
				'top'     => 'yes',
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
<script>
	function switchTab() {
		UITabs.get().switchTab('functioning');
	}
</script>
