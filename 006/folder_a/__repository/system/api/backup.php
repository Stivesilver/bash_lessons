<?PHP
	Security::init();

	$tsRefid = io::get('tsrefid');
	$table = io::get('table');
	$constr = io::get('constr');
	$search_key = io::get('search_key');
	$search_id = io::get('search_id');

	io::css('.mpSelected', 'background-color: #BBB !important; color: #000 !important; border: 1px solid #888');
	io::css('.trItem', 'border-radius: 5px; margin: 1px; padding: 0px 10px 0px 10px');

	$tw = new UITreeWrapper(
		CoreUtils::getURL(
			'./backup.ajax.php', 
			array(
				'tsrefid' => $tsRefid, 
				'table' => $table, 
				'search_key' => $search_key,
				'search_id' => $search_id
			)
		),
		'tw'
	);

	$tree = $tw->getTree()
		->iconSize(32)
		->selectionClassName('mpSelected');

	$tw->toolButtons(true);

	$tw->leftFrameWidth('40%');

	$tw->className('zBox9 zLightLines');

	$tw->addItemProcess(
		'yr',
		CoreUtils::getURL('./backup_process.php', array('tsrefid' => $tsRefid, 'table' => $table, 'constr' => $constr))
	);

	echo $tw->toHTML();
?>
