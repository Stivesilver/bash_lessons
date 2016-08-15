<?php
	Security::init();

	$RefID = io::posti('id', true);
	$form = db::execSQL("
			SELECT *
              FROM webset.std_iep
             WHERE siepmrefid = $RefID
    ")->assocAll();
se($form);
	io::download($form);
?>
