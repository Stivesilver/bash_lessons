<?PHP
	Security::init(NO_OUTPUT | MODE_WS, 1);
	$task_file = io::get('task_file');

	if (!file_exists($task_file) || !is_file($task_file)) {
		die();
	}

	$xml_task = file_get_contents($task_file);
	$task = CryptClass::factory()->decode($xml_task);

	$task = new SimpleXMLElement($task);

	$template_main = $task->xpath('/task/template');
	$template_kids = $template_main[0]->children();
	$template = $template_kids[0]->asXML();

	$root_id = $task->xpath('/task/root_id');
	$root_id = (string)$root_id[0];

	$sql = $task->xpath('/task/sql');
	$sql = (string)$sql[0];

	$ideaData = IDEAData::factory();
	$data = $ideaData->xmlExport(
		$template,
		$root_id
	);
	$data = explode("\n", $data);
	unset($data[0]);
	$data = implode("\n", $data);

	$task = '
		<task>
			<bigtemplate>' . $template . '</bigtemplate>
			<data>' . $data . '</data>
			<sql>' . $sql . '</sql>
		</task>
	';

	$task = CryptClass::factory()->encode($task);
	echo $task;
?>
