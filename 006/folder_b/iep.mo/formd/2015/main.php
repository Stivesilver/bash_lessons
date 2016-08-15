<?php
	Security::init();
	$dskey   = io::get('dskey');
	$tabs = new UITabs('tabs');
	$tabs->addTab('Assessments (Part 1)')->url(CoreUtils::getURL('part1.php', array_merge($_GET, array('nexttab' => '1'))));
	$tabs->addTab('MAP Accommodations (Part 2)')->url(CoreUtils::getURL('part2.php', array('dskey' => $dskey, 'nexttab' => '2')));
	$tabs->addTab('ACT (Part 3)')->url(CoreUtils::getURL('part3.php', array_merge($_GET, array('nexttab' => '3'))));
	$tabs->addTab('MAP-A (Part 4)')->url(CoreUtils::getURL('part4.php', array_merge($_GET, array('nexttab' => '4'))));
	$tabs->addTab('ACCESS FOR ELLS (Part 5)')->url(CoreUtils::getURL('part5.php', $_GET));
	print $tabs->toHTML();
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
