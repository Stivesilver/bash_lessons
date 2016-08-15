<?php

    Security::init();
   
    $aitems = io::post('aitems');

	$items = db::execSQL("
		SELECT pmdesc
		  FROM webset.disdef_progmod acc
		  	   LEFT OUTER JOIN webset.disdef_progmodcat cat ON cat.catrefid = acc.catrefid
		 WHERE refid in (" . $aitems . ")		 
		 ORDER BY cat.seqnum, categor, acc.seqnum, pmdesc
	")->indexCol(0);
		
	io::ajax('text', implode("\r\n", $items));
?>