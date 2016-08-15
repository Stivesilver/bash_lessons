<?php
    Security::init();
    $dskey   = io::get('dskey');

    $tabs = new UITabs('tabs');
        $tabs->addTab('Program Modifications and Accommodations')->url(CoreUtils::getURL('progmod.php', $_GET));
        $tabs->addTab('Other')->url(CoreUtils::getURL('../../iep/constructions/main.php', array_merge($_GET, array('constr'=>121, 'iep'=>'no'))));

    print $tabs->toHTML();
?>