<?php

	Security::init();
	$screening_id = io::get('screening_id');
	$tsRefID = io::get('tsRefID');
	$evalproc_id = io::get('evalproc_id');
	$student = IDEAStudentEval::factory($tsRefID, $evalproc_id);
	$red_summary = $student->getREDSummary();
	$red_text = '';
	if (isset($red_summary[$screening_id])) {
		$description = $red_summary[$screening_id]['red_desc'];
		$summary =  $red_summary[$screening_id]['red_text'];
		$arr = array();
		if ($description != '') $arr[] = $description;
		if ($summary != '') $arr[] = $summary;
		$red_text = implode(PHP_EOL, $arr);
	}

	$checked = db::execSQL("
		SELECT red_assneed
		  FROM webset.es_std_red
		 WHERE screening_id = $screening_id
		   AND evalproc_id = $evalproc_id
	")->getOne();

	io::ajax('red', $red_text);
	io::ajax('checked', $checked);
?>
