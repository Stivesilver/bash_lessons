<?php
	Security::init();
    
	$RefIDs = explode(',', io::post('RefID'));
	for ($i=0; $i < sizeOf($RefIDs); $i++) {
		if($RefIDs[$i]>0) {  
            DBImportRecord::factory('webset.std_fif_forms', 'sfrefid')
                ->key('sfrefid', $RefIDs[$i])
                ->set('archived', "CASE COALESCE(archived,'N') WHEN 'Y' THEN NULL ELSE 'Y' END ", true)
                ->set('lastuser', db::escape(SystemCore::$userUID))
                ->set('lastupdate', 'NOW()', true)
                ->import();
		}
    }
?>