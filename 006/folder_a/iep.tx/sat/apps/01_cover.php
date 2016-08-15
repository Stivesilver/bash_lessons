<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);

	$tabs->addTab(
		'General',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/cover_page_general.php', array(
				'dskey'   => $dskey,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Signatures and Dates',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php',
			array(
				'dskey'   => $dskey,
				'constr'  => '86',
				'nexttab' => '-1',
				'top'     => 'yes',
			)
		),
		'sign&dates'
	);

	echo $tabs->toHTML();

	print FFInput::factory()
		->name('screenURL')
		->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))
		->hide()
		->toHTML();

?>

<script>

	function switchTab(id) {
		var tab1 = UITabs.get('tabs');
		if (id >= 0) {
			tab1.switchTab(id);
		} else {
			api.goto($('#screenURL').val());
		}
	}


</script>