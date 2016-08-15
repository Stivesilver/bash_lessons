<?php

	Security::init();

	$RefID = io::geti('RefID');

	$result = db::execSQL("
		SELECT siepmdocfilenm,
               pdf_files,
               form_ids,
               file_obj
          FROM webset_tx.std_fie_arc
	     WHERE siepmrefid = $RefID
	")->assoc();

	if ($result['file_obj'] === null) {
		$fileName = $result['siepmdocfilenm'];
		io::download(SystemCore::$secDisk . '/Iep/' . $fileName);
	}

?>