<?php
	Security::init();
	$dskey = io::get('dskey');
	$area = io::get('area');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab('Committee Members')->url(CoreUtils::getURL('meet_participants.php', array_merge($_GET, array('area'=>$area))));
	$tabs->addTab('Agreements')->url(CoreUtils::getURL('meet_agreements.php', array_merge($_GET, array('area'=>$area))));
	
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