<?php

	Security::init();
	
	$RefID  	= io::geti('RefID');
	$ds 	 	= DataStorage::factory($dskey, true);
    $area		= io::get('area');
    $goal 		= new IDEAGoalDBHelper($area);
	$sql 		= $goal->getQueryDActive($RefID);

	db::execSQL($sql);
	
?>
