<?php

	Security::init();

	$where = '';

	if (io::post('type') == 'b') {
		$where = 'WHERE sbb_brefid = ' . io::posti('id');
	} else {
		$where = 'WHERE sbg_grefid = ' . io::posti('id');
	}

	$row = db::execSQL("
		SELECT *
		  FROM webset.std_progress_reporting
		$where
		")->fields;

	$extentProgress = json_decode($row['spr_period_data'], true);
	$smpRefID = io::posti('smpRefID');

	unset($extentProgress[$smpRefID]);

	$newPeriodData = db::escape(json_encode($extentProgress));
	if ($newPeriodData != "[]") {
		db::execSQL("
		UPDATE webset.std_progress_reporting SET spr_period_data = '$newPeriodData' $where
	");
	} else {
		db::execSQL("
		DELETE FROM webset.std_progress_reporting $where
	");
	}
	io::ajax('finish', 1);

?>
