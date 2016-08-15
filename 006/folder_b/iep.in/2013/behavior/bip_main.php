<?php
    Security::init();
    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);

    $tabs = new UITabs('tabs');
    $tabs->addTab('General Info')->url(CoreUtils::getURL('general.php', array('dskey' => $dskey)));
    $tabs->addTab('Behavior Goals')->url(CoreUtils::getURL('goals.php', array('dskey' => $dskey)));
    $tabs->addTab('Narrative')->url(CoreUtils::getURL('action.php', array('dskey' => $dskey)));
    $tabs->addTab('BIP Items')->url(CoreUtils::getURL('items.php', array('dskey'=>$dskey, 'mode'=>'B')));

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
