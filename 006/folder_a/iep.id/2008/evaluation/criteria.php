<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds    = DataStorage::factory($dskey, true);
	$tabs  = new UITabs('tabs');

	$tabs->indent(2);

	$tabs->addTab(
		'Criteria',
		CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
				'dskey'   => $dskey,
				'constr'  => '35',
				'nexttab' => 1,
				'top'     => 'yes',
				'print'     => 'no'
			)
		)
	);

	$tabs->addTab(
		'Eligibility Determination',
		CoreUtils::getURL(
			'/apps/idea/iep.id/2008/evaluation/eligibility.php', array(
				'dskey'   => $dskey,
				'tsRefID' => 0,
				'iep'     => 'no',
				'nexttab' => '-1',
				'top'     => 'yes'
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
