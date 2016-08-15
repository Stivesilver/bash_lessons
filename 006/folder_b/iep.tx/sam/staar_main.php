<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab('General')->url(CoreUtils::getURL('staar_yesno.php', array_merge($_GET, array('assess'=>'STAAR'))));
	$tabs->addTab('Subject')->url(CoreUtils::getURL('staar_subject.php', array_merge($_GET, array('assess'=>'STAAR'))));
	$tabs->addTab('Rationale')->url(CoreUtils::getURL('staar_rationale.php', array_merge($_GET, array('assess'=>'STAAR'))));
	$tabs->addTab('Success Initiative')->url(CoreUtils::getURL('staar_initiative.php', array_merge($_GET, array('assess'=>'STAAR'))));
	$tabs->addTab('Accelerated Instruction')->url(CoreUtils::getURL('staar_accelerated.php', array_merge($_GET, array('assess'=>'STAAR'))));

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
			tab1.disableTab(2, false);
			tab1.disableTab(3, false);
			tab1.disableTab(4, false);
		} else {
			tab1.disableTab(1, true);
			tab1.disableTab(2, true);
			tab1.disableTab(3, true);
			tab1.disableTab(4, true);
		}
	}
</script>