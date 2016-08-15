<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);

	$tabs->addTab(
		'Language/Communicative Status',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/sources_data_list.php', array(
				'dskey'   => $dskey,
				'nexttab' => 0,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Results and Interpretations',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/fie/apps/res_and_int_edit.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => '-1',
				'top'     => 'yes'
			)
		)
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