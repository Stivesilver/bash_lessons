<?php
    function appAttach($RefID, &$data) { 
        $spconsid = io::posti('spconsid');
        if (isset($spconsid)) {        
            DBImportRecord::factory('webset.std_spconsid', 'sscmrefid')
                ->key('sscmrefid', $spconsid)
                ->set('saveapp', 'Y')
                ->set('lastuser', db::escape(SystemCore::$userUID))
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }
    }
?>
