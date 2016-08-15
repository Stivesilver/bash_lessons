<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab('TELPAS Reading')->url(CoreUtils::getURL('telpas_rpte.php', $_GET));
	$tabs->addTab('TELPAS')->url(CoreUtils::getURL('telpas_telpop.php', $_GET));

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
	
	function adjustTabs(status) {
		var tab1 = UITabs.get('tabs');
		if (status == 'Y') {    
			tab1.disableTab(1, false);
		} else {
			tab1.disableTab(1, true);
		}
	}
</script>