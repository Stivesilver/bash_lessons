<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');
	
	$tabs->indent(2);
	
	$tabs->addTab(
		'Question', 
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/sld/f_yesno.php', array(
			'dskey'   => $dskey,
			'nexttab' => 0,
			'top'     => 'yes'
			)
		)
	);
	
	$tabs->addTab(
		'Assessment/Documentation', 
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/sld/f_tests.php', 
			array(
					'dskey'   => $dskey,
					'nexttab' => 1,
					'top'     => 'yes'
			)
		)
	);
	
	$tabs->addTab(
		'Impact', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey'   => $dskey,
			'constr'  => '98',
			'nexttab' => '-1',
			'top'     => 'yes'
			)
		)
	);
	
	echo $tabs->toHTML();
	echo FFInput::factory()->name('screenURL')->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))->hide()->toHTML();

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