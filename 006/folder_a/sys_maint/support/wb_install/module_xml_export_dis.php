<?PHP
	$vndrefid = $_GET["vnd"] > 0 ? $_GET["vnd"] : 2;
	Security::init(NO_OUTPUT | MODE_WS, $vndrefid);
	$iniOptions = IDEAFormat::getIniOptions();
	$template = $iniOptions['install_district_defaults_xml'];
	$ideaData = IDEAData::factory();
	$data = $ideaData->xmlExport(
		$template,
		$vndrefid
	);
	$data = explode("\n", $data);
	unset($data[0]);
	$data = implode("\n", $data);
	echo "
<task>
	$template
		$data
</task>
";
?>
