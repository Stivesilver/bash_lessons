<?php

	Security::init();

	$en_refid = io::geti('en_refid');
    
    db::execSQL("
    	DELETE FROM webset.std_bgb_assessment_entry
    	 WHERE en_refid = $en_refid
    ");
?>