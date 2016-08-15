<?php
	
	function save_reason($RefID, &$data) {

        db::execSQL("
        	DELETE FROM webset.std_in_iepconfpurpose
        	 WHERE stdrefid = " . $RefID . "
        ");

        $RefIDs = explode(',', io::post('purpose'));
        $arrOther = db::execSQL("
			SELECT siepcprefid
			  FROM webset.statedef_iepconfpurpose
			 WHERE LOWER(siepcpdesc) LIKE '%other%'
        ")->indexCol(0);

        for ($i=0; $i < sizeOf($RefIDs); $i++) {
	        if ($RefIDs[$i] > 0) {
	            $dbrec = DBImportRecord::factory('webset.std_in_iepconfpurpose', 'sicprefid')
	                ->set('stdrefid', $RefID)
	                ->set('siepcprefid', $RefIDs[$i])
	                ->set('lastuser', db::escape(SystemCore::$userUID))
	                ->set('lastupdate', 'NOW()', true);
	            if (in_array($RefIDs[$i], $arrOther) and io::post('other') != '') {
	            	$dbrec->set('sicpnarrative', io::post('other'));
				}
				$dbrec->import();
	        }
	    }
	}
    
?>
