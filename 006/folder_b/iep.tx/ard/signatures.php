<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);

	$tabs->addTab(
		'Committee Members',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/ard/meet_participants_list.php', array(
				'dskey'   => $dskey,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0,
				'area'    => io::get('area'),
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Agreements',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php',
			array(
				'dskey'   => $dskey,
				'constr'  => '86',
				'nexttab' => '-1',
				'top'     => 'yes'
			)
		)
	);

	echo $tabs->toHTML();

?>