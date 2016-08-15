<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab('Academic')->url(CoreUtils::getURL('academic.php', $_GET));
	$tabs->addTab('Mainstream')->url(CoreUtils::getURL('mainstream.php', $_GET));
	$tabs->addTab('Supplementary')->url(CoreUtils::getURL('suppl_serv.php', $_GET));
	$tabs->addTab('Related Services')->url(CoreUtils::getURL('related.php', $_GET));
	$tabs->addTab('Transportation')->url(CoreUtils::getURL('trans_serv.php', $_GET));
	$tabs->addTab('ESY Services')->url(CoreUtils::getURL('esy_serv.php', $_GET));

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