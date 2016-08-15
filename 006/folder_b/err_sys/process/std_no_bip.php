<?php
    $bip_req_sw = db::execSQL("
		SELECT bip_req_sw 
		  FROM webset.std_in_fbp_sum 
		 WHERE stdrefid = " . $tsRefID . "        
    ")->getOne();
	
    $bip_records = db::execSQL("
		SELECT count(1) 
		  FROM webset.std_in_bipgen 
		 WHERE stdrefid = " . $tsRefID . "        
    ")->getOne();

    if ($bip_req_sw == 'Y' && $bip_records == '0') {
        return true;
    } else {
        return false;
    }
?>