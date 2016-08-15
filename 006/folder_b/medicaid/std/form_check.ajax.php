<?php

	Security::init();

	$msmRefId = io::posti('msm_refid');

	$SQL  = "
		SELECT msf_file_content,
			   msf_file_name
		  FROM webset.med_std_form
		 WHERE msf_refid = $msmRefId
	";

	$dataFile = db::execSQL($SQL)->assoc();

	$file = FileRW::factory()
		->write(base64_decode($dataFile['msf_file_content']))
		->save(FileRW::PROTECTED_BY_USER, array_pop(explode('.', $dataFile['msf_file_name'])))
		->getPath();

	io::ajax('res', $msmRefId);
	io::download($file);

?>