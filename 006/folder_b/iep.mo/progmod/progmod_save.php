<?php
    Security::init();

    $dskey   = io::get('dskey');
    $ds        = DataStorage::factory($dskey);    
    $tsRefID   = $ds->safeGet('tsRefID');
    
    # Delete old progmod
    $SQL = "
        DELETE FROM webset.std_progmod
         WHERE stdrefid = ".$tsRefID."
           AND stsrefid = ".io::posti('stsrefid')."
    ";    
    db::execSQL($SQL);
    
    #Location save
    $RefIDs = explode(',', io::post('malrefid'));  
    for ($i=0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i]>0) { 
            DBImportRecord::factory('webset.std_progmod', 'refid')
               ->set('stdrefid', $tsRefID)
               ->set('typeofval', 'loc')
               ->set('stsrefid', io::posti('stsrefid'))
               ->set('val', 'on')
               ->set('val_id', $RefIDs[$i])
               ->set('lastuser', db::escape(SystemCore::$userUID))
               ->set('lastupdate', 'NOW()', true)
               ->import();
        }
    }
    
    #Frequency Save
    $RefIDs = explode(',', io::post('esfumrefid'));  
    for ($i=0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i]>0) {           
            DBImportRecord::factory('webset.std_progmod', 'refid')
               ->set('stdrefid', $tsRefID)
               ->set('typeofval', 'frq')
               ->set('stsrefid', io::posti('stsrefid'))
               ->set('val', 'on')
               ->set('val_id', $RefIDs[$i])
               ->set('lastuser', db::escape(SystemCore::$userUID))
               ->set('lastupdate', 'NOW()', true)
               ->import();            
        }
    }
    
    #Other
    if (io::post('stsrefid_other')!='') {
        DBImportRecord::factory('webset.std_progmod', 'refid')
           ->set('stdrefid', $tsRefID)
           ->set('typeofval', 'oth')
           ->set('stsrefid', io::posti('stsrefid'))
           ->set('val', db::escape(io::post('stsrefid_other')))
           ->set('lastuser', db::escape(SystemCore::$userUID))
           ->set('lastupdate', 'NOW()', true)
           ->import();         
    }
    
    #Beginning Date
    if (io::post('begdate')!='') {
        DBImportRecord::factory('webset.std_progmod', 'refid')
           ->set('stdrefid', $tsRefID)
           ->set('typeofval', 'beg')
           ->set('stsrefid', io::posti('stsrefid'))
           ->set('val', "TO_CHAR('".io::post('begdate')."'::DATE, 'mm/dd/yyyy')", true)
           ->set('lastuser', db::escape(SystemCore::$userUID))
           ->set('lastupdate', 'NOW()', true)
           ->import();        
    }
    
    #Ending Date
    if (io::post('enddate')!='') {
        DBImportRecord::factory('webset.std_progmod', 'refid')
           ->set('stdrefid', $tsRefID)
           ->set('typeofval', 'end')
           ->set('stsrefid', io::posti('stsrefid'))
           ->set('val', "TO_CHAR('".io::post('enddate')."'::DATE, 'mm/dd/yyyy')", true)
           ->set('lastuser', db::escape(SystemCore::$userUID))
           ->set('lastupdate', 'NOW()', true)
           ->import();
    }

    
    if (io::post('finishFlag')=='yes') {
        io::js('
            var edit1 = EditClass.get(); 
            edit1.cancelEdit();
        ');
    } else {
        io::js('
            var edit1 = EditClass.get(); 
            edit1.restoreDefault();
        ');
    }
?>
