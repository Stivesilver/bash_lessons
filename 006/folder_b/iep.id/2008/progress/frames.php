<?php

    Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);
	$tabs->addTab(
		'Goals with Grades',
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/progress/goals_with_grades.php',
			array(
				'dskey' => $dskey,
			)
		)
	);

	$tabs->addTab(
		'Goals',
		CoreUtils::getURL(
			'/apps/idea/iep/documentation/pr_progrepMain.php',
			array(
				'dskey' => $dskey,
				'ESY' => 'N',
			)
		)
	);

	print $tabs->toHTML();

?>