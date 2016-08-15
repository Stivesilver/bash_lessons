<?php

	Security::init();
	$tabs = new UITabs('tabs');
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs->indent(3);

	$tabs->addTab(
		'Eligibility Determination Team',
		CoreUtils::getURL('./edt_list.php',
			array(
				'dskey' => $dskey,
				'next_tab' => 1
			)
		)
	);

	$tabs->addTab(
		'SLD Members',
		CoreUtils::getURL(
			'./sld_edit.php',
			array(
				'dskey' => $dskey,
				'next_tab' => -1
			)
		)
	);

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
