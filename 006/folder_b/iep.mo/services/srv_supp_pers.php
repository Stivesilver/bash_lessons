<?php
    Security::init();
    
    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);
    $tsRefID   = $ds->safeGet('tsRefID');
	
    $tabs = new UITabs('tabs');
    $tabs->addTab('Supports For School Personnel')
        ->url(CoreUtils::getURL('srv_supp_pers_list.php', array('dskey'=>$dskey)));
    $tabs->addTab('Program Modifications')
        ->url(CoreUtils::getURL('srv_supp_pers_other.php', array('dskey'=>$dskey)));
    print $tabs->toHTML();    
?>