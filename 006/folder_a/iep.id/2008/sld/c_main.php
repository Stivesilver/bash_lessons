<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);
	
	$tabs->addTab(
		'Areas of Concern', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey'   => $dskey,
			'constr'  => '103',
			'nexttab' => 1,
			'top'     => 'yes'
			)
		)
	);
	
	$tabs->addTab(
		'Assessments', 
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/sld/c_assessments_list.php', 
			array(
					'dskey'   => $dskey,
					'top'     => 'yes',
					'area_id' => 104
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