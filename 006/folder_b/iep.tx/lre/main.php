<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab('Efforts')->url(CoreUtils::getURL('efforts.php', array_merge($_GET, array('mode'=>'E'))));
	$tabs->addTab('Reasons')->url(CoreUtils::getURL('reasons.php', array_merge($_GET, array('mode'=>'E'))));
	$tabs->addTab('Options')->url(CoreUtils::getURL('efforts.php', array_merge($_GET, array('mode'=>'O'))));
	$tabs->addTab('Rejected')->url(CoreUtils::getURL('reasons.php', array_merge($_GET, array('mode'=>'O'))));
	$tabs->addTab('Statements')->url(CoreUtils::getURL('statement.php', $_GET));

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