<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab(
		'Description of evaluation', 
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', 
			array(
				'constr' => '141',
				'dskey' => $dskey,
				'top' => 'no',
				'iep' => 'no',				
				'nexttab' => '1'
				
			)
		)
	);
	
	$tabs->addTab('Tests')->url(CoreUtils::getURL('assessment.php', array('dskey' => $dskey)));

	print $tabs->toHTML();
	print FFInput::factory()
		->name('screenURL')
		->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))
		->hide()
		->toHTML();
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