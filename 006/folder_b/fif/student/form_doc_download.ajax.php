<?php
	Security::init();

	$RefID = io::posti('RefID', true);

	$form = db::execSQL("
        SELECT uploaded_content,
               uploaded_filename
          FROM webset.std_fif_forms
         WHERE sfrefid = " . $RefID . "
    ")->assoc();

	$file = SystemCore::$tempPhysicalRoot . '/' . $form['uploaded_filename'];
	file_put_contents($file, base64_decode($form['uploaded_content']));

	io::download(SystemCore::$tempVirtualRoot . '/' . $form['uploaded_filename']);
?>