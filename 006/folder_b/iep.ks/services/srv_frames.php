<?php
    Security::init();
    $dskey   = io::get('dskey');
    $ds      = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    $tabs = new UITabs('tabs');

        $tabs->addTab('Services')
            ->url(CoreUtils::getURL('srv_list.php', array('dskey'=>$dskey)));

        $tabs->addTab('Amended Services')
            ->url(CoreUtils::getURL('srv_list.php', array('dskey'=>$dskey, 'smode'=>'A')));

    print $tabs->toHTML();

?>