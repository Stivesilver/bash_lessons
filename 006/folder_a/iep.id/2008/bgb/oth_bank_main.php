<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');
	
	$tabs->indent(3);
	
	$tabs->addTab(
		'Goal Bank', 
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/bgb/oth_bank_list.php', array(
			'dskey'   => $dskey,
			'nexttab' => 0,
			'top'     => 'yes'
			)
		)
	);
	
	$tabs->addTab(
		'Measure', 
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/bgb/oth_items_list.php', 
			array(
					'dskey'   => $dskey,
					'area'	  => 'measure',
					'nexttab' => 1,
					'top'     => 'yes'
			)
		)
	);
	
	$tabs->addTab(
		'Schedule', 
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/bgb/oth_items_list.php',  
			array(
					'dskey'   => $dskey,
					'area'	  => 'schedule',
					'nexttab' => '-1',
					'top'     => 'yes'
			)
		)
	);
	
	echo $tabs->toHTML();
	echo FFInput::factory()->name('screenURL')->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))->hide()->toHTML();
   	 
?>