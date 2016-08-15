<?php
    Security::init();
    
    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $esy        = io::get('ESY');
    
    #Baseline List
    $list = new ListClass('baseline');    
    $list->title = ($esy=='Y'?'ESY ':'').'Current Progress Maintenance';
    $list->hideCheckBoxes = true;
            
    $list->SQL = "
       SELECT * FROM (SELECT grefid,
                             COALESCE(gsentance, overridetext) as gsentance,
                             NULL as bsentance,
                             percentofprogress,     
                             NULL as brefid
                        FROM webset.std_bgb_goal goal
                             INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
                       WHERE goal.stdrefid = ".$tsRefID."
                             AND baseline.siymrefid = ".$stdIEPYear."
                             AND baseline.esy = '".$esy."'
                       UNION ALL 
                      SELECT goal.grefid,
                             --COALESCE(gsentance, goal.overridetext) as gsentance,
                             null,
                             COALESCE(bsentance, benchmark.overridetext) as bsentance,
                             benchmark.percentofprogress,       
                             brefid
                        FROM webset.std_bgb_goal goal
                             INNER JOIN webset.std_bgb_baseline baseline ON goal.blrefid = baseline.blrefid
                             INNER JOIN webset.std_bgb_benchmark benchmark ON goal.grefid = benchmark.grefid
                       WHERE goal.stdrefid = ".$tsRefID."
                             AND baseline.siymrefid = ".$stdIEPYear."
                             AND baseline.esy = '".$esy."'
                     ) as t
       ORDER BY 1";    
    
    $list->addColumn('Goal');
    $list->addColumn('Benchmark');
    $list->addColumn('Progress')->type('progress');                
    
    $list->editURL = 'javascript:api.goto("'.
        CoreUtils::getURL('bgb_current_progress_add.php', array('dskey'=>$dskey, 'ESY'=>$esy, 'grefid'=>'AF_REFID', 'brefid'=>'AF_COL4')).
    '");';
    
    $list->printList();      
?>  