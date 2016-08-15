<?php

	Security::init(NO_OUPUT);

	$id = io::post('id', TRUE);

	io::ajax('statement', db::execSQL("
		 SELECT ssgitext
           FROM webset.es_formdisselections
          WHERE ssgirefid = " . $id . "
	")->getOne());
?>
