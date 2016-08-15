<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(6);

	$tabs->addTab(
		'Programs',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/23_programs_list.php', array(
				'dskey'   => $dskey                 ,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0                      ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'Campus Rules',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/23_campus_rules_list.php',
			array(
				'dskey'     => $dskey            ,
				'nexttab'   => 1                 ,
				'top'       => 'yes'             ,
				'key_name'  => 'physical_results',
				'lasttab'   => 0
			)
		)
	);

	$tabs->addTab(
		'Grade Appropriate Citizenship Skills',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/23_citizenship_skills_list.php',
			array(
				'dskey'     => $dskey,
				'nexttab'   => 2     ,
				'top'       => 'yes' ,
				'lasttab'   => 0
			)
		)
	);

	$tabs->addTab(
		'Social Skills Training',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/23_social_skills_list.php',
			array(
				'dskey'     => $dskey,
				'nexttab'   => 3     ,
				'top'       => 'yes' ,
				'lasttab'   => 0
			)
		)
	);

	$tabs->addTab(
		'Active Supervision And Monitoring',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/23_monitoring_list.php',
			array(
				'dskey'     => $dskey,
				'nexttab'   => 4     ,
				'top'       => 'yes' ,
				'lasttab'   => 0
			)
		)
	);

	$tabs->addTab(
		'Observations/Management',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/23_management_edit.php',
			array(
				'dskey'     => $dskey,
				'nexttab'   => -1    ,
				'top'       => 'yes' ,
				'lasttab'   => 0
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