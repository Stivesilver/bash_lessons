<?PHP
	$vndrefid = $_GET["vnd"];
	if (empty($vndrefid)) {
		die("District ID is not set");
	}
	Security::init(NO_OUTPUT | MODE_WS, $vndrefid);
	
	$iniOptions = IDEAFormat::getIniOptions();
	$SQL = $iniOptions['install_registration_sql'];

	db::execSQL($SQL);

	echo "Registration Completed";
?>
