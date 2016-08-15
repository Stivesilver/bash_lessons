<?php
    Security::init();
    
    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    
	$list = new ListClass(); 
    
    $list->SQL = "
        SELECT umfirstname  || ' ' || umlastname,
               umlastname  || ', ' || umfirstname,
               umtitle,
               umssn
          FROM sys_usermst
         WHERE vndrefid = VNDREFID
               ADD_SEARCH
           AND COALESCE(um_internal, TRUE) IS TRUE
         ORDER BY 2
    ";
                   
    $list->showSearchFields = true;
    $list->addSearchField('Last Name', "lower(umlastname)  like '%' || lower(ADD_VALUE)|| '%'", 'text');
    $list->addSearchField('School', 'sys_usermst', 'list')
		->sql('
            SELECT vourefid,
		           vouname
              FROM public.sys_voumst
             WHERE vndrefid = VNDREFID
             ORDER by vouname
        ');
        
    $list->addColumn('Name'); 
    $list->addColumn('Title'); 
    
    $list->editURL = 'javascript:assignNames("AF_REFID", "AF_COL3")';  

    $list->printList();
    
?>
<script type='text/javascript'>
    function assignNames(name, id) {
        api.window.dispatchEvent('user_selected', {name: name, id: id});
        api.window.destroy();        
    }
</script>
