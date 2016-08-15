<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->indent(5);

	$tabs->addTab(
		'State/District Accommodations', CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey' => $dskey,
			'constr' => '144',
			'nexttab' => '1'
			)
		)
	);

	$tabs->addTab(
		'Entrance Exam Accommodations', CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey' => $dskey,
			'constr' => '145',
			'top' => 'no',
			'nexttab' => '2'
			)
		)
	);

	$tabs->addTab(
		'Behavior Intervention Planning', CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey' => $dskey,
			'constr' => '146',
			'top' => 'no',
			'desktop' => 'yes'
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