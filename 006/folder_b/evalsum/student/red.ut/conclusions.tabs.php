<?
  	Security::init();

    $dskey = io::get('dskey');

    $tabs = new UITabs('tabs');

    $tabs->addTab('General')
        ->url(CoreUtils::getURL('conclusions.php', array('dskey'=>$dskey)));

    $tabs->addTab('Participants')
        ->url(CoreUtils::getURL('participants.php', array('dskey'=>$dskey)));

    print $tabs->toHTML();


?>