<?php
    Security::init();

    $tabs = new UITabs('tabs');

    $tabs->addTab('Export Data')
        ->url(CoreUtils::getURL('export.php'));

    $tabs->addTab('Import Data')
        ->url(CoreUtils::getURL('import.php'));

    print $tabs->toHTML();

?>