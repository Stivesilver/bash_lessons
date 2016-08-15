<?PHP
	$vndrefid = $_GET["vnd"];
	$task_file = $_GET["file"];
	if (empty($task_file)) {
		die("XML data file is not set");
	}
	if (empty($vndrefid)) {
		die("District ID is not set");
	}
	Security::init(NO_OUTPUT | MODE_WS, $vndrefid);
	
	$xml_task = file_get_contents($task_file);
	$task = new SimpleXMLElement($xml_task);
	$template = $task->xpath('/task/template');
	$template = $template[0]->asXML();
	$data = $task->xpath('/task/public.sys_vndmst');
	$data = $data[0]->asXML();
	
	$data = IDEAData::factory()->xmlImport(
		$template,
		$vndrefid,
		$data
	);
?>
