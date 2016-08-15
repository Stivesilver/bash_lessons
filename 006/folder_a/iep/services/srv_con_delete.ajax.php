<?php
	Security::init();

    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);
    $tsRefID   = $ds->safeGet('tsRefID');
    
	$RefIDs = explode(',', io::post('RefID'));  
	for ($i=0; $i < sizeOf($RefIDs); $i++) {
		if($RefIDs[$i]>0) { 
            $SQL = "
                DELETE FROM webset.std_placementselected 
                 WHERE stdrefid = ".$tsRefID."
                   AND EXISTS (SELECT 1 
                                 FROM webset.std_placementconsiderations
                                WHERE pcdrefid = sspsdrefid
                                  AND spcrefid = ".$RefIDs[$i].")
            ";          
            db::execSQL($SQL);

            $SQL = "
                DELETE FROM webset.std_placementconsiderations WHERE spcrefid = ".$RefIDs[$i]."
            ";          
            db::execSQL($SQL);
		}
    }
?>