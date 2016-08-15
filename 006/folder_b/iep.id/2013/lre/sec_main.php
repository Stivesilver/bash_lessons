<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->indent(5);

	$tabs->addTab(
		'Least Restrictive Environment', CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey' => $dskey,
			'constr' => '147',
			'nexttab' => '1',
			'top' => 'no'
			)
		)
	);
	

	$tabs->addTab(
		'Educational Environment for Ages 6-21', CoreUtils::getURL(
			'place_cat.php', array('dskey' => $dskey, 'age' => 'k12')
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