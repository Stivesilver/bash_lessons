<?php

	Security::init();
	
	$benchmark_id = io::geti('benchmark_id');
	$is_entry = io::get('is_entry');
	
	$benchmark = db::execSQL("
		SELECT goal.grefid,
		       baseline.stdrefid		       
		  FROM webset.std_bgb_baseline baseline
			   INNER JOIN webset.std_bgb_goal goal ON goal.blrefid = baseline.blrefid
			   INNER JOIN webset.std_bgb_benchmark benchmark ON goal.grefid = benchmark.grefid
		 WHERE brefid = $benchmark_id
	")->assoc();
	
	$goal_id = $benchmark['grefid'];
	$tsRefID = $benchmark['stdrefid'];
	
	$dskey = file_get_contents(SystemCore::$tempPhysicalRoot . "/bgb_dskey_" . session_id() . "_" . $tsRefID . ".txt");
	
	if ($is_entry == 'true') {
		$url = './bgb_assessment_entry_list.php';		
	} else {		
		$url = './bgb_assessment_list.php';
	}
	
	$url = CoreUtils::getURL($url, array('goal_id' => $goal_id, 'benchmark_id' => $benchmark_id, 'dskey' => $dskey));
	
	header('Location: ' . $url);

?>