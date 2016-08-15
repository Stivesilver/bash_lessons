<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->indent(5);

	$tabs->addTab(
		'Services', CoreUtils::getURL('srv_spedmst.php', array('dskey' => $dskey))
	);

	$tabs->addTab(
		'Supplementary Aids', CoreUtils::getURL('srv_supmst.php', array('dskey' => $dskey))
	);

	$tabs->addTab(
		'Service Delivery', CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey' => $dskey,
			'constr' => '39',
			'top' => 'no',
			'nexttab' => '3'
			)
		)
	);

	$tabs->addTab(
		'Other Considerations', CoreUtils::getURL(
			'/apps/idea/iep/constructions/main.php', array(
			'dskey' => $dskey,
			'constr' => '21',
			'top' => 'yes',
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
