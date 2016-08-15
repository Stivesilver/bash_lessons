<?php
    Security::init();
    $dskey   = io::get('dskey');
    $ds      = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    
    $tabs = new UITabs('tabs');
    
        $tabs->addTab('Placement Considered')
            ->url(CoreUtils::getURL('/apps/idea/iep/services/srv_con_place.php', array('dskey'=>$dskey)));
            
        $tabs->addTab('Placement Selected')
            ->url(CoreUtils::getURL('/apps/idea/iep/services/srv_sel_place.php', array('dskey'=>$dskey)));

        $tabs->addTab('Selected Decisions')
            ->url(CoreUtils::getURL('/apps/idea/iep/services/srv_sel_place_dec.php', array('dskey'=>$dskey)));
            
        $tabs->addTab('Placement Considerations')
        	->url(CoreUtils::getURL('/apps/idea/iep/services/srv_placecon.php', array('dskey'=>$dskey)));
        
    print $tabs->toHTML();
        
?>