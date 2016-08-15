<?php
    Security::init();
    
    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    
	$list = new ListClass(); 
    
    $list->SQL = "
        SELECT COALESCE(stdfnm,'') || ' ' || COALESCE(stdlnm,''),
               COALESCE(stdlnm,'') || ', ' || COALESCE(stdfnm,''),
               'Student'
          FROM webset.dmg_studentmst dmg
               INNER JOIN webset.sys_teacherstudentassignment ts ON dmg.stdrefid = ts.stdrefid
         WHERE tsrefid = ".$tsRefID."
         UNION ALL
       (SELECT COALESCE(gdfnm,'') || ' ' || COALESCE(gdlnm,''),
               COALESCE(gdlnm,'') || ', ' || COALESCE(gdfnm,''),
               CASE gtdesc IS NULL WHEN TRUE THEN 'Guardian' ELSE gtdesc END
          FROM webset.dmg_guardianmst grd
               INNER JOIN webset.def_guardiantype ON grd.gdtype = webset.def_guardiantype.gtrefid
               INNER JOIN webset.sys_teacherstudentassignment ts ON grd.stdrefid = ts.stdrefid
         WHERE tsrefid = ".$tsRefID."
         ORDER BY gtrank)
         UNION ALL
       (SELECT umfirstname  || ' ' || umlastname,
               umlastname  || ', ' || umfirstname,
               umtitle
          FROM sys_usermst
         WHERE vndrefid = VNDREFID
               ADD_SEARCH
           AND COALESCE(um_internal, TRUE) IS TRUE
         ORDER BY 2)
    ";
                   
    $list->showSearchFields = true;
    $list->addSearchField('Last Name', "lower(umlastname)  like '%' || lower(ADD_VALUE)|| '%'", 'text');
    $list->addSearchField('Title', "lower(umtitle)  like '%' || lower(ADD_VALUE)|| '%'", 'text');
        
    $list->addColumn('Name'); 
    $list->addColumn('Title'); 
    
    $list->editURL = 'javascript:assignNames("AF_REFID", "AF_COL2")';  

    $list->printList();
    
?>
<script type='text/javascript'>
    function assignNames(name, title) {
        api.window.dispatchEvent('user_selected', {name: name, title: title});
        api.window.destroy();        
    }
</script>
