<?php
    Security::init();
    
    $where   = '';
    $area    = io::get('area');
    $gsearch = io::get("gsearch");
    
    $list = new ListClass(); 
    
    if ($gsearch!="") {
        $where = "
            AND (
                 (lower(smaname)  like '%' || lower('%" . $gsearch . "%') || '%')  OR
                 (lower(sminame)  like '%' || lower('%" . $gsearch . "%') || '%')  OR
                 (lower(phpurl)   like '%' || lower('%" . $gsearch . "%') || '%')  OR
                 (lower(sqltable) like '%' || lower('%" . $gsearch . "%') || '%')
            )
        ";
    }

    //For reports and district setup must have current state filter
    if (in_array(io::get("area"), array("D","R"))) {
        $where .= " AND (screfid = ".VNDState::factory()->id." or screfid = -1) ";
    } 
    
    $list->SQL = "
        SELECT smirefid,
               COALESCE(state, 'ALL') || ' - ' || smaname,
               sminame,
               sqltable,
               replace(phpurl, '/applications/webset/', '/apps/idea/'),
               staterefid,
               smarefid
          FROM webset.sped_sm_items
               LEFT JOIN webset.sped_sm_area USING(smarefid)
               LEFT JOIN webset.glb_statemst ON glb_statemst.staterefid = screfid
         WHERE area='".$area."'
           AND (webset.sped_sm_items.expdate>now() OR webset.sped_sm_items.expdate IS NULL) 
               ADD_SEARCH
               ".$where."
         ORDER BY COALESCE(state, 'ALL'), webset.sped_sm_area.seqnum, smaname, sped_sm_items.seqnum, sminame
    ";
                   
    $list->showSearchFields = true;
    
    if ($area=='S' or substr(SystemCore::$userUID,0,8)!='gsupport') 
    $list->addSearchField('State')
        ->value(io::get('state'))
        ->sqlField("lower(COALESCE(state,'All'))    like '%' || lower(ADD_VALUE) || '%'")
        ->size(4);
    
    $list->addSearchField('Area')
        ->sqlField("
            ((lower(smaname)  like '%' || lower(ADD_VALUE) || '%')  OR
             (lower(sminame)  like '%' || lower(ADD_VALUE) || '%')  OR
             (lower(phpurl)   like '%' || lower(ADD_VALUE) || '%')  OR
             (lower(sqltable) like '%' || lower(ADD_VALUE) || '%'))
        ")
        ->size(12);
    
   
    $list->addColumn('Category')->type('group'); 
    $list->addColumn('Area'); 
    
    $list->editURL = 'javascript:openApp("'.SystemCore::$virtualRoot.'AF_COL4")';  

    $list->printList();
    
?>
<script type='text/javascript'>
    function openApp(url) {
        parent.$('#rightFrame').attr('src', url);
    }
</script>
