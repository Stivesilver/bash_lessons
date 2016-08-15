<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->indent(5);

	$tabs->addTab(
		'PPT meeting', CoreUtils::getURL(
			'./team_meeting_edit.php',
			array(
				'dskey' => $dskey,
				'nexttab' => '1'
			)
		)
	);

	$tabs->addTab(
		'Team Member Present', CoreUtils::getURL(
			'./iep_participants.php', array('dskey' => $dskey)
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
