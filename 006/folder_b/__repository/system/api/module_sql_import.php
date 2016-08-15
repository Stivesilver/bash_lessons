<?PHP
	$sql_file = $_GET['sql_file'];
	$vndrefid = $_GET['vndrefid'];

	if (!file_exists($sql_file) || !is_file($sql_file)) {
		die();
	}

	if (!($vndrefid > 0)) {
		die();
	}

	Security::init(NO_OUTPUT | MODE_WS, $vndrefid);

	$sql = file_get_contents($sql_file);
	$sql = CryptClass::factory()->decode($sql);

	if ($sql != '') {
		db::execSQL($sql);
	}

?>
