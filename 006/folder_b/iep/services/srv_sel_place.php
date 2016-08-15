<?php
    Security::init();

    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);
    $tsRefID   = $ds->safeGet('tsRefID');

    if (IDEACore::disParam(51)=="Y") {
        $tabs = new UITabs('tabs');
        $tabs->addTab('Placement Selected')
            ->url(CoreUtils::getURL('srv_sel_place_list.php', array('dskey'=>$dskey, 'nexttab' => 0)));
        $tabs->addTab('Placement Selected Decisions')
            ->url(CoreUtils::getURL('srv_sel_place_dec.php', array('dskey'=>$dskey, 'nexttab' => 1)));
        print $tabs->toHTML();
    } else {
        header('Location: ' . CoreUtils::getURL('srv_sel_place_list.php', array('dskey'=>$dskey)));
    }
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
