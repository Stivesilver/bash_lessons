<?php
	Security::init();

    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);
    $tsRefID   = $ds->safeGet('tsRefID');
    
	$RefIDs = explode(',', io::post('RefID'));  
	for ($i=0; $i < sizeOf($RefIDs); $i++) {
		if ($RefIDs[$i]>0) { 
            $SQL = "
                DELETE FROM webset.std_progmod
                 WHERE stdrefid = ".$tsRefID."
                   AND stsrefid = ".$RefIDs[$i]."
            ";         
            db::execSQL($SQL);
		}
    }
?>