<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(3);

	$tabs->addTab(
		'A. Enrollment History',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/enrollment_history_edit.php', array(
				'dskey'   => $dskey                 ,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0                      ,
				'top'     => 'yes'
			)
		)
	);

	$tabs->addTab(
		'A. Previous Schools Attended',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/03_schools_list.php',
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
		'B. Attendance History',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/04_ahistory_edit.php',
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
