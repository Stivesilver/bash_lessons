<?php
    Security::init();
    $dskey   = io::get('dskey');
    
    $tabs = new UITabs('tabs');
        $tabs->addTab('Transition Services')->url(CoreUtils::getURL('formCservices.php', $_GET));
        $tabs->addTab('Course of Study')->url(CoreUtils::getURL('formCcourse.php', $_GET));
        $tabs->addTab('Graduation')->url(CoreUtils::getURL('formCgraduation.php', $_GET));        
        
    print $tabs->toHTML();
?>