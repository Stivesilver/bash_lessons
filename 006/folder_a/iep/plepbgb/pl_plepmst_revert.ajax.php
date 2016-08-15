<?php

	Security::init();

	$param = json_decode(io::get('param'));

	$values = IDEABackup::factory($param->tsrefid, $param->constr)->getValues($param->refid);

	$arr = array();
	$xml = new SimpleXMLElement($values);

	foreach ($xml->xpath('//value') as $child) {
		$arr[(string)$child['name']] = (string)$child;
	}

	io::ajax('arr', $arr);
?>
