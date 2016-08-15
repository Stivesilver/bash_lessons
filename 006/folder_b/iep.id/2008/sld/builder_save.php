<?php
	Security::init();
    
    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);    
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear'); 
	$student 	= IDEAStudent::factory($tsRefID);
	$filename = SystemCore::$tempPhysicalRoot. "/" . $tsRefID.'.xml';
    
    if (!file_exists($filename)) {
        Security::init();
        io::err('Sorry. IEP was not archived. Please Re-Build IEP and save again.', true);
    } else {
        $xml_cont = base64_encode(file_get_contents($filename));
       
        #Archive PDF Forms    
        $RefIDs   = explode(',', io::get('f_str'));
        $form_ids = '';
        for ($i = 0; $i < sizeOf($RefIDs); $i++) {
            if ($RefIDs[$i] > 0) {  
                DBImportRecord::factory('webset.std_forms', 'smfcrefid')
                    ->key('smfcrefid', $RefIDs[$i])
                    ->set('archived', 'Y')
                    ->set('lastuser', db::escape(SystemCore::$userUID))
                    ->set('lastupdate', 'NOW()', true)
                    ->import();
                $form_ids .= $RefIDs[$i] . ',';
            }
            
        }
        
        $form_ids = substr($form_ids, 0, -1);
        
        #Add IEP
        DBImportRecord::factory('webset.es_std_esarchived', 'esarefid')
           ->set('xml_cont', $xml_cont)
           ->set('stdrefid', $tsRefID)     //esadate, esaname,
           ->set('esadate',  io::get('IEPDate')) 
           ->set('esaname',  'Specific Learning Disability - ' . io::get('ReportType'))					
           ->set('lastuser', SystemCore::$userUID)
           ->set('lastupdate', 'NOW()', true)
           ->import(); 
    }
    
    Header('Location: ' . CoreUtils::getURL('builder.php', array('dskey'=>$dskey, 'drefid' => $drefid)));
?>