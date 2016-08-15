<?php

	Security::init();
	error_reporting(0);

	require_once(SystemCore::$physicalRoot . "/uplinkos/classes/pdfClass.v2.0.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$result = db::execSQL("
    	SELECT xml_cont,
    	       doc_path,
    	       uploaded_file
    	  FROM webset.es_std_esarchived
    	 WHERE esarefid = " . io::geti('RefID')
	);

	$doc_path = $result->fields['doc_path'];
	$xml_cont = $result->fields['xml_cont'];
	$uploaded_file = $result->fields['uploaded_file'];


	io::download(SystemCore::$tempVirtualRoot . '/' . $form['uploaded_filename']);
	if ($doc_path != '') {
		io::download(SystemCore::$secDisk . '/Eval/' . $doc_path);
	} elseif ($uploaded_file != '') {
		$data = explode('_filename_divider_', $uploaded_file);
		$file = SystemCore::$tempPhysicalRoot . '/' . $data[0];
		file_put_contents($file, base64_decode($data[1]));
		io::download(SystemCore::$tempVirtualRoot . '/' . $data[0]);
	} elseif ($xml_cont != '') {
		$doc = new xmlDoc();

		$doc->edit_mode = 'no';
		$doc->xml_data = base64_decode($xml_cont);
		$fileName = $doc->getPdf();

		io::download($_SERVER['DOCUMENT_ROOT'] . $fileName);
	}
?>
