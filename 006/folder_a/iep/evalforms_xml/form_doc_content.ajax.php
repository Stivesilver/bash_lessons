<?php
	Security::init();

	$file = SystemCore::$tempPhysicalRoot . '/' . basename(io::post('upload_control_file'));

	if (file_exists($file)) {
		if (!file_exists($_SESSION["s_secDisk"] . "/Iep")) mkdir($_SESSION["s_secDisk"] . "/Iep");
		$filename  = "iep_" . $_GET["tsRefID"] . "_" . date( "mdYhis" ) .".pdf";
		$path = $_SESSION["s_secDisk"] . "/Iep/" . basename(io::post('upload_control_file'));
		copy($file, $path);

		io::ajax('uploaded_filename', basename(io::post('upload_control_file')));
		io::ajax('uploaded_content', $path);
	} else {
		io::err('file has not been uploaded');
	}
?>
