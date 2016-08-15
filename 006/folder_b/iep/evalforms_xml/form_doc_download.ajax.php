<?php
	Security::init();

	$RefID = io::posti('RefID', true);

	$form = db::execSQL("
        SELECT uploaded_filename
          FROM webset.std_forms_xml
         WHERE sfrefid = " . $RefID . "
    ")->getOne();

	io::download($form);
?>
