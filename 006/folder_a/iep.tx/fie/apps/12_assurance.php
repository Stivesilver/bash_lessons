<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);

	$tabs->addTab(
		'Assurances',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/12_participant_list.php', array(
				'dskey'   => $dskey,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Copy',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/12_save_edit.php',
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

?>