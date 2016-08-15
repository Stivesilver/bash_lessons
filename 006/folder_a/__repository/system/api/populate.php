<?PHP
	Security::init();

	$dskey = io::get('dskey');
	$path = io::get('path', true);
	$area = io::get('area');

	io::css('.mpSelected', 'background-color: #BBB !important; color: #000 !important; border: 1px solid #888');
	io::css('.trItem', 'border-radius: 5px; margin: 1px; padding: 0px 10px 0px 10px');

	$tw = new UITreeWrapper(
		CoreUtils::getURL('./populate.ajax.php', array('dskey' => $dskey)),
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
		CoreUtils::getURL($path, array('dskey' => $dskey, 'path' => $path, 'area' => $area))
	);

	echo $tw->toHTML();
?>
