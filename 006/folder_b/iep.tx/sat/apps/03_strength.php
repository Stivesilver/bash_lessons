<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);

	$tabs->addTab(
		'Academic',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey'   => $dskey                 ,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0                      ,
				'top'     => 'yes'                  ,
				'constr'  => 85
			)
		)
	);

	$tabs->addTab(
		'Social Skills/Behavior',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/03_social.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => -1    ,
				'top'     => 'yes' ,
				'lasttab' => 0
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