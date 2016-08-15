<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(3);

	$tabs->addTab(
		'Transition from Part C Services Plan',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'constr' => '209',
				'dskey' => $dskey,
				'nexttab' => 1,
				'help' => '209',
				'top' => 'no'
			)
		)
	);

	$tabs->addTab(
		'Transitioning Procedures',
		CoreUtils::getURL(
			'./trans_proced_list.php',
			array(
				'dskey'   => $dskey,
				'nexttab' => 2
			)
		)
	);

	$tabs->addTab(
		'Case Notes',
		CoreUtils::getURL(
			'/apps/idea/iep/casenotes/cn_casenotes.php', array(
				'dskey' => $dskey,
				'nexttab' => 3,
				'help' => '179',
				'top' => 'no'
			)
		)
	);

	$tabs->addTab(
		'Planning Conference',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'constr' => '211',
				'dskey' => $dskey,
				'help' => '211'
			)
		)
	);

	echo $tabs->toHTML();
	echo FFInput::factory()->name('screenURL')->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))->hide()->toHTML();

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
