<?php

	Security::init();
	
	$refId = io::post('RefID');
	$arrId = explode(',', $refId);
	$count = count($arrId);
	
	if ($count > 0) {
		$sql = "
			UPDATE webset.std_bgb_baseline
			   SET siymrefid = stdschoolyear,
			       stdrefid = (
					SELECT stdrefid
					  FROM webset.std_iep_year iep
					 WHERE iep.siymrefid = stdschoolyear
			       ),
			       stdschoolyear = NULL
			 WHERE blrefid IN (" . implode(',', array_map('intval', $arrId)) . ")
			   AND stdschoolyear IS NOT NULL;


			UPDATE webset.std_bgb_goal
			   SET blrefid = gprefid,
				   stdrefid = (
					SELECT iep.stdrefid
					  FROM webset.std_iep_year iep
						   INNER JOIN webset.std_bgb_baseline bl ON iep.siymrefid = bl.siymrefid
					 WHERE webset.std_bgb_goal.blrefid = bl.blrefid
				   ),
				   gprefid = NULL
			 WHERE gprefid IN (" . implode(',', array_map('intval', $arrId)) . ")
			   AND gprefid IS NOT NULL;

			UPDATE webset.std_bgb_benchmark
			   SET grefid = bprefid,
				   stdrefid = (
					SELECT iep.stdrefid
					  FROM webset.std_iep_year iep
						   INNER JOIN webset.std_bgb_baseline bl ON iep.siymrefid = bl.siymrefid
					 WHERE webset.std_bgb_goal.blrefid = bl.blrefid
				   ),
				   bprefid = NULL
			  FROM webset.std_bgb_goal
			 WHERE webset.std_bgb_goal.grefid = webset.std_bgb_benchmark.bprefid
			   AND blrefid IN (" . implode(',', array_map('intval', $arrId)) . ")
			   AND webset.std_bgb_benchmark.bprefid IS NOT NULL;
		";
		
		db::execSQL($sql);
	}
 
?>
