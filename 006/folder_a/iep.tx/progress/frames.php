<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab('Standards Report')->url(CoreUtils::getURL('standard_main.php', array_merge($_GET, array('ESY'=>'N'))));
	$tabs->addTab('General Report')->url(CoreUtils::getURL('progrep_main.php', array_merge($_GET, array('ESY'=>'N'))));
	$tabs->addTab('Mainstream')->url(CoreUtils::getURL('mainstream_main.php', array_merge($_GET, array('ESY'=>'N'))));

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