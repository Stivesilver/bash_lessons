<?php

	Security::init(); 

	require_once(SystemCore::$physicalRoot . '/uplinkos/classes/pdfClass.v2.0.php'); 
	require_once(SystemCore::$physicalRoot . '/applications/webset/iep.tx/dataentry/build.inc.php');
	
	$file_path = SystemCore::$physicalRoot . '/uplinkos/temp/IEP_' . io::geti('id') . '.pdf';

	$data = db::execSQL("
		SELECT * 
		  FROM webset_tx.std_dataentry 
		 WHERE refid = " . io::geti('id') ."
	")->assoc();
	
	buildDoc($data, $file_path); 
	io::download($file_path);
?>