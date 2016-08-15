<?php
    Security::init();
    
    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    
	$list = new ListClass(); 
    
    $list->title = 'ESY Goals';
    $list->hideNumberColumn = true;
    
    $list->SQL = "
        SELECT grefid, 
               baseline.order_num || '.' || goal.order_num,
               COALESCE(overridetext,gsentance)
          FROM webset.std_bgb_goal goal
               INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
         WHERE goal.stdrefid = ".$tsRefID."
           AND baseline.siymrefid = ".$stdIEPYear."
           AND baseline.esy = 'Y'
         ORDER BY baseline.order_num, baseline.blrefid, goal.order_num, goal.grefid
    ";                   
        
    $list->addColumn('Order'); 
    $list->addColumn('Goal'); 
    
    $list->editURL = 'javascript:assignGoal("AF_COL1")';  

    $list->printList();
    
?>
<script type='text/javascript'>
    function assignGoal(goal) {
        api.window.dispatchEvent('goal_selected', {goal: goal});
        api.window.destroy();        
    }
</script>
