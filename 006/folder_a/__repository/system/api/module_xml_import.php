<?PHP
	$task_file = $_GET['task_file'];
	$vndrefid = $_GET['vndrefid'];

	if (!file_exists($task_file) || !is_file($task_file)) {
		die();
	}

	if (!($vndrefid > 0)) {
		die();
	}


	Security::init(NO_OUTPUT | MODE_WS, $vndrefid);

	$xml_task = file_get_contents($task_file);
	$task = CryptClass::factory()->decode($xml_task);

	$task = new SimpleXMLElement($task);

	$template_main = $task->xpath('/task/bigtemplate');
	$template_kids = $template_main[0]->children();
	$template = $template_kids[0]->asXML();

	$data = $task->xpath('/task/data');
	$data = $data[0]->children();
	$data = $data[0]->asXML();

	$sql = $task->xpath('/task/sql');
	$sql = (string)$sql[0];
	$root_id = db::execSQL($sql)->getOne();

	$oldData = IDEAData::factory()->xmlExport($template, $root_id);
	$oldData = explode("\n", $oldData);
	unset($oldData[0]);
	$oldData = implode("\n", $oldData);
	$oldDataObj = new SimpleXMLElement($oldData);
	$oldDataCount = count($oldDataObj[0]->children());

	#merge data only if district has no any data in this area
	if ($oldDataCount == 0) {
		$ideaData = IDEAData::factory();
		if ($template != '' && $root_id != '' && $data != '') {
			$ideaData->xmlImport($template, $root_id, $data);
		}
	} else {
		echo "District already has $oldDataCount records. Omited.";
	}
?>
