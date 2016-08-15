<?php
  
  	Security::init();
  	
  	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');
	
	$tabs->indent(3);
	$tabs->addTab(
		'Services',
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/services/srv_srv_list.php',
			array(
				'dskey' => $dskey,
			)
		)
	);
	
	$tabs->addTab(
		'Service Delivery',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', 
			array(
				'dskey'   => $dskey,
				'constr'  => '39',
				'nexttab' => 2,
				'top'     => 'no'
			)
		)
	);

	
	$tabs->addTab(
		'Considerations',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey'   => $dskey,
			'constr'  => '21',
			'nexttab' => 4,
			'top'     => 'yes'
			)
		)
	);


	print $tabs->toHTML();
	print FFInput::factory()->name('screenURL')->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))->hide()->toHTML();
  
?>

<script type="text/javascript">
	function switchTab(id) {
		var tab1 = UITabs.get('tabs');
		if (id >= 0) {
			tab1.switchTab(id);
		} else {
			api.goto($('#screenURL').val());
		}
	}
</script>