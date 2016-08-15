<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);

	$tabs = new UITabs('tabs');
	$tabs->addTab('ARD/IEP Dates')->url(CoreUtils::getURL('../iepmeeting/meet_iepdates.php', $_GET));
	$tabs->addTab('Assessment')->url(CoreUtils::getURL('../iepmeeting/meet_rad_dates.php', $_GET));
	$tabs->addTab('Related Services')->url(CoreUtils::getURL('../iepmeeting/meet_more.php', $_GET));
	$tabs->addTab('Other')->url(CoreUtils::getURL('other.php', $_GET));
	$tabs->addTab('Parents Concerns')->url(CoreUtils::getURL('concerns.php', $_GET));

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