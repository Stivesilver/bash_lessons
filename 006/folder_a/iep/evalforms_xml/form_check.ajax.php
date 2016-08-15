<?php
	Security::init();

	$RefID = io::posti('RefID', true);

	$form =  db::execSQL("
        SELECT *
          FROM webset.std_forms_xml
         WHERE sfrefid = " . $RefID . "
    ")->assoc();

	$uploaded = false;
	if ($form['uploaded_filename'] != '') {
		$uploaded = true;
	}

	io::ajax('RefID', $RefID);
	io::ajax('uploaded', $uploaded);
?>
