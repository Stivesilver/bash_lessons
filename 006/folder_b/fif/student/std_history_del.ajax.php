<?php
	Security::init();
    
    $RefIDs = explode(',', io::post('RefID'));
    for ($i=0; $i < sizeOf($RefIDs); $i++) {
        if ($RefIDs[$i] > 0) {  
            DBImportRecord::factory('webset.std_fif_history', 'hisrefid')
                ->key('hisrefid', $RefIDs[$i])
                ->set('stdrefid', null)
                ->set('deleted_id', 'stdrefid', true)
                ->set('lastuser', "'".db::escape(SystemCore::$userUID)."' || '<' || stdrefid::varchar || '>'" , true)
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }
    }
?>