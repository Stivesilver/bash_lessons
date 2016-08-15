<?php
    Security::init();
    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);

    $tabs = new UITabs('tabs');
    $tabs->addTab('Intake Info')->url(CoreUtils::getURL('intake.php', array('dskey' => $dskey)));
    $tabs->addTab('Assessment')->url(CoreUtils::getURL('assessment.php', array('dskey' => $dskey)));
    $tabs->addTab('Summary')->url(CoreUtils::getURL('summary.php', array('dskey' => $dskey)));

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