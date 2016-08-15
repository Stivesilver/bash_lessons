<?php
	
    function processYears($RefID, &$data) {
        
        //Set all student IEP year as none current
        DBImportRecord::factory('webset.std_iep_year', 'stdrefid')
            ->key('stdrefid', $data['stdrefid'])
            ->set('siymcurrentiepyearsw', null)
            ->import();              
            
        //Set newly added IEP yeas as current
        DBImportRecord::factory('webset.std_iep_year', 'siymrefid')
            ->key('siymrefid', $RefID)
            ->set('siymcurrentiepyearsw', 'Y')
            ->set('lastuser', db::escape(SystemCore::$userUID))
            ->set('lastupdate', 'NOW()', true)
            ->import();  
    }
    	
?>
