<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	if (io::get('area') == 1) {
		$tabs = new UITabs('tabs');
		$tabs->addTab('Interventions and Accommodations')->url(CoreUtils::getURL('progmod.php', $_GET));
		$tabs->addTab('Behavior Intervention Plan')->url(CoreUtils::getURL('bip.php', $_GET));

		print $tabs->toHTML();
		print FFInput::factory()
				->name('screenURL')
				->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))
				->hide()
				->toHTML();
	} else {
		io::js('api.goto(' . json_encode(CoreUtils::getURL('progmod.php', $_GET)) . ');', TRUE);
	}
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