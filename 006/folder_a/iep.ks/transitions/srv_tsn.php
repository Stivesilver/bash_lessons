<?php
    Security::init();
    $dskey   = io::get('dskey');
    $ds      = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');

    $tabs = new UITabs('tabs');
    $tabs->addTab('Courses/Experiences')
        ->url(CoreUtils::getURL('srv_courses.php', array('dskey'=>$dskey)));
    $tabs->addTab('Other Experiences ')
        ->url(CoreUtils::getURL('srv_other.php', array('dskey'=>$dskey)));
    $tabs->addTab('Anticipated PS Goals')
        ->url(CoreUtils::getURL('srv_postgoals.php', array('dskey'=>$dskey)));
    $tabs->addTab('Graduation Information')
        ->url(CoreUtils::getURL('srv_graduation.php', array('dskey'=>$dskey)));
    $tabs->addTab('Areas of Interests')
        ->url(CoreUtils::getURL('srv_interest.php', array('dskey'=>$dskey)));

    print $tabs->toHTML();

?>