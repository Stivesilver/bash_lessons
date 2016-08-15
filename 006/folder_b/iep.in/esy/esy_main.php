<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab('ESY Questions')->url(CoreUtils::getURL('questions.php', array('dskey' => $dskey)));
	$tabs->addTab('ESY Recommendation')->url(CoreUtils::getURL('recommendation.php', array('dskey' => $dskey)));
	$tabs->addTab('ESY Services')->url(CoreUtils::getURL('../services/srv_spedmst.php', array('dskey' => $dskey, 'ESY' => 'Y')));

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