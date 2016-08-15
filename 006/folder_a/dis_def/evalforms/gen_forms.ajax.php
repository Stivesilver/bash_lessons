<?php

	Security::init();

	CoreUtils::increaseTime();
	CoreUtils::increaseMemory();

	$selValSrt = io::get('selVal');
	$selVal = explode(',', $selValSrt);
	$doc = new RCDocument(RCPageFormat::A4, RCDocumentFormat::PDF);
	foreach ($selVal AS $val) {
		$fb_cont = db::execSQL("
			SELECT fb_content,
				   title
			  FROM webset.disdef_forms
			 WHERE dfrefid = $val
		")->index();
		FBDocument::factory($fb_cont[0])
			->printPages($doc);
		$doc->addBookmark($fb_cont[1]);
	}

	io::download($doc->compile());
?>
