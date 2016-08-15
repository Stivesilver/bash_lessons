<?php
	Security::init();

	$RefID = io::posti('RefID', true);

	$form = db::execSQL("
        SELECT uploaded_filename
          FROM webset.es_std_evalproc_forms
         WHERE frefid = " . $RefID . "
    ")->getOne();

	io::download($form);
?>
