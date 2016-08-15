<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(3);

	$tabs->addTab(
		'Outcome/Action Steps',
		CoreUtils::getURL(
			'./bgb/bgb_main.php', array(
				'dskey' => $dskey,
				'nexttab' => 1,
				'ESY' => 'N',
				'top' => 'no'
			)
		)
	);

	$tabs->addTab(
		'Review/Changes',
		CoreUtils::getURL(
			'./review_list.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => 2
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
