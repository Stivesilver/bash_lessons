<?php
	Security::init();
 
    set_time_limit(0);
    
   	$process_xml = db::execSQL("
		SELECT sql_body
		  FROM webset.sys_sql_archive
		 WHERE refid = " . io::geti('RefID') . " 
	")->getOne();
	
	$ids = io::get('ids');
	if ($ids != '') {
		$procesTree = new SimpleXMLElement($process_xml);
		$procesTree->servers = null;
		foreach(array_unique(explode(',', $ids)) as $id) {
			if ($id > 0) {
				$procesTree->servers->addChild('server', $id);
			}	
		}
		$process_xml = $procesTree->asXML();
	}
	
	$xml_data = IDEAIntegrity::serverProcess($process_xml, true);
	
	//FileUtils::createTmpFile($xml_data, 'xml');
    
    io::ajax('RefID', io::geti('RefID'));
    io::ajax('dskey', IDEAIntegrity::genReport($process_xml, $xml_data));
    
?>