<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');

	$tabs->addTab('Tracking')
		->url(CoreUtils::getURL('/apps/idea/iep/constructions/main.php', array('constr' => 116, 'dskey' => $dskey, 'top' => 'no', 'nexttab' => 1)));

	$tabs->addTab('Post-School Goals 1-2')
		->url(CoreUtils::getURL('/apps/idea/iep/constructions/main.php', array('constr' => 30, 'dskey' => $dskey, 'nexttab' => 2)));

	$tabs->addTab('Graduation Consideration')
		->url(CoreUtils::getURL('/apps/idea/iep/constructions/main.php', array('constr' => 31, 'dskey' => $dskey, 'nexttab' => 3)));

	$tabs->addTab('ISAT')
		->url(CoreUtils::getURL('/apps/idea/iep/constructions/main.php', array('constr' => 32, 'dskey' => $dskey, 'nexttab' => '-1')));

	print $tabs->toHTML();
	print FFInput::factory()->name('screenURL')->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))->hide()->toHTML();
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