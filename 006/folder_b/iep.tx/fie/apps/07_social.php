<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(3);

	$tabs->addTab(
		'Sources',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/07_sources_list.php', array(
				'dskey'   => $dskey,
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
				'key_name'  => 'social_emotional',
				'lasttab'   => 0
			)
		)
	);

	$tabs->addTab(
		'Strengths/Weaknesses',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/07_weakness_list.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => '-1',
				'area'    => 2,
				'top'     => 'yes'
			)
		),
		'strengths'
	);

	echo $tabs->toHTML();

	echo FFInput::factory()
		->name('screenURL')
		->value(
			CoreUtils::getURL(
				$ds->safeGet('screenURL'),
				array('dskey' => $dskey)))
		->hide()
		->toHTML();

?>
<script>
	function switchTab() {
		UITabs.get().switchTab('strengths');
	}
</script>