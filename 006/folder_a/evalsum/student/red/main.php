<?

	Security::init();

	$tsRefID = io::get('RefID');
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudentEval($tsRefID);
	$evalproc_id = $student->get('evalproc_id');

	if (!($evalproc_id > 0)) {
		$message = "Please create Active Evaluation Process in New Student Evaluation Screen first.";
		print UIMessage::factory($message, UIMessage::NOTE)->toHTML();
		die(); 
	}

	$apps = array();

	$apps[] = array(
		'name' => 'General',
		'url' => 'general.php',
		'params' => array('dskey' => $dskey)
	);

	if (IDEACore::disParam(99) == 'Y') {
		$apps[] = array(
			'name' => 'Disability/Services',
			'url' => '/apps/idea/evalsum/student/constructions/main.php',
			'params' => array('constr' => '83',
			'dskey' => $dskey,
			'list' => 'yes',
			'cop' => 'no',
			'iep' => 'no')
		);
	}

	$apps[] = array(
		'name' => 'Summary',
		'url' => 'summary.php',
		'params' => array('dskey' => $dskey)
	);

	$apps[] = array(
		'name' => 'Conclusions',
		'url' => 'conclusions.tabs.php',
		'params' => array('dskey' => $dskey)
	);

	$apps[] = array(
		'name' => 'Parent Notification',
		'url' => '/apps/idea/evalsum/student/constructions/main.php',
		'params' => array('constr' => '88',
		'dskey' => $dskey,
		'list' => 'yes',
		'cop' => 'no',
		'iep' => 'no')
	);

	$apps[] = array(
		'name' => 'Options',
		'url' => '/apps/idea/evalsum/student/constructions/main.php',
		'params' => array('constr' => '124',
		'dskey' => $dskey,
		'cop' => 'no',
		'top' => 'no',
		'iep' => 'no')
	);

	$apps[] = array(
		'name' => 'Preview RED',
		'url' => 'preview.php',
		'params' => array('dskey' => $dskey)
	);

	$tabs = new UITabs('tabs');
	$tabs->indent(5);
	foreach ($apps as $app) {
		$tabs->addTab($app['name'], CoreUtils::getURL($app['url'], $app['params']));
	}

	print $tabs->toHTML();
?>
