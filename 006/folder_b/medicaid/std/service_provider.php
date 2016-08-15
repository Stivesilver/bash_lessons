<?php
	Security::init();

	if (db::execSQL("SELECT 1 FROM webset.med_disdef_providers WHERE umrefid = " . SystemCore::$userID)->EOF)
		io::err("You are not assigned in Service Provider. ");

	$mp_refid = db::execSQL("SELECT mp_refid FROM webset.med_disdef_providers WHERE umrefid = " . SystemCore::$userID)->getOne();
	echo UISCTimeScale::factory(SCTimeScale::MEDICAID_PROVIDER, $mp_refid)
		->toHTML();
?>
