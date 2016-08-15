<?php

    Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);
	$tabs->addTab(
		'Frequency Progress Reporting',
		CoreUtils::getURL(
			'/apps/idea/iep/services/srv_freq_prog.php',
			array(
				'dskey' => $dskey,
				'nexttab' => '1'
			)
		)
	);

	$tabs->addTab(
		'How Progress will be Reported',
		CoreUtils::getURL(
			'/apps/idea/iep/services/srv_meth_info.php',
			array(
				'dskey' => $dskey,
			)
		)
	);

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
