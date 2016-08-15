<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(4);

	$tabs->addTab(
		'A. Strengths and Needs',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php',
			array(
				'dskey'   => $dskey,
				'constr'  => '40',
				'nexttab' => 0,
				'top'     => 'no'
			)
		)
	);

	$tabs->addTab(
		'A. Assessments',
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/evaluation/assessments_list.php',
			array(
				'dskey' => $dskey,
			)
		)
	);

	$tabs->addTab(
		'B. Lack of Instruction',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey'   => $dskey,
				'constr'  => '33',
				'nexttab' => '2',
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'C. LEP',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey'   => $dskey,
				'constr'  => '34',
				'nexttab' => '-1',
				'top'     => 'yes'
			)
		)
	);

	print $tabs->toHTML();

?>