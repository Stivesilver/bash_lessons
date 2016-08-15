<?php

	Security::init();

	$param = json_decode(io::get('param'));

	$values = IDEABackup::factory($param->tsrefid, 'webset.sped_ini_set', $param->constr)->getValues($param->refid, true);

	$arr = array();
	$xml = new SimpleXMLElement($values);

	foreach ($xml->xpath('//value') as $child) {
		$arr[(string)$child['name']] = base64_decode((string)$child) ? base64_decode((string)$child) : (string)$child; 
	}
	
	io::ajax('arr', $arr);
?>
