<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(4);
	$tabs->addTab(
		'Subjects',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/04_academic_subjects_list.php', array(
				'dskey'   => $dskey                 ,
				'tsRefID' => $ds->safeGet('tsRefID'),
				'nexttab' => 0                      ,
				'top'     => 'yes'                  ,
			)
		)
	);

	$tabs->addTab(
		'Retained',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/04_academic_retained_edit.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => 1     ,
				'top'     => 'yes' ,
				'lasttab' => 0
			)
		)
	);

	$tabs->addTab(
		'Achievement Test Data',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/04_academic_achievement_data_list.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => 2     ,
				'top'     => 'yes' ,
				'lasttab' => 0
			)
		)
	);

	$tabs->addTab(
		'TAKS',
		CoreUtils::getURL(
			'/apps/idea/iep.tx/sat/apps/04_academic_taks_list.php',
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