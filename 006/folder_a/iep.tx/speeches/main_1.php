<?php

	Security::init();

	$dskey   = io::get('dskey');
	$ds      = DataStorage::factory($dskey, true);
	$tsRefID = $ds->safeGet('tsRefID');
	$tabs    = new UITabs('tabs');

	$tabs->indent(5);

	$tabs->addTab(
		'Sources Of Data',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/data_source_list.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 0       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Summary',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/summary_edit.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 1       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Language Assessment',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/language_list.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 2       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Informal Assessment',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/informal.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => 3       ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Articulation',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/speeches/articulation.php', array(
				'dskey'   => $dskey  ,
				'tsRefID' => $tsRefID,
				'nexttab' => '-1'    ,
				'top'     => 'yes'
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