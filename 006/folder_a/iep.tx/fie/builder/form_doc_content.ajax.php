<?php

	Security::init();

	$file = SystemCore::$tempPhysicalRoot . '/' . basename(io::post('upload_control_file'));
	if (file_exists($file)) {
		io::ajax('uploaded_filename', basename(io::post('upload_control_file')));
		io::ajax('uploaded_content', base64_encode(file_get_contents($file)));
	} else {
		io::err('file has not been uploaded');
	}

?>