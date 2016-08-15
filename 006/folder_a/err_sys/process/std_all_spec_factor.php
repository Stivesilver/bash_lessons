<?php

	$linkedQuestions = db::execSQL("
		SELECT scalinkrefid
		  FROM webset.std_spconsid std
		  	   INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
	     WHERE stdrefid = " . $tsRefID . "
		   AND std.scarefid IS NOT NULL
		   AND syrefid = " . $stdIEPYear . "
		   AND scalinkrefid > 0
		   AND scalinkrefid NOT IN (SELECT scqrefid
									  FROM webset.std_spconsid
								     WHERE stdrefid = " . $tsRefID . "
									   AND scarefid IS NOT NULL
									   AND syrefid = " . $stdIEPYear . ")
	")->indexCol(0);

	$linkedSQL = "
		SELECT scmrefid, scmsdesc
		  FROM webset.statedef_spconsid_quest
		 WHERE scmrefid in (" . (count($linkedQuestions)>0 ? implode(',', $linkedQuestions) : '0') . ")
	";

	$notLinkedSQL = "
		SELECT scmrefid, scmsdesc
		  FROM webset.statedef_spconsid_quest
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND scmlinksw = 'N'
		   AND scmrefid NOT IN (SELECT scqrefid
								  FROM webset.std_spconsid
								 WHERE stdrefid = " . $tsRefID . "
								   AND scarefid IS NOT NULL
								   AND syrefid = " . $stdIEPYear . ")
		   AND (recdeactivationdt IS NULL OR now()< recdeactivationdt)
		 ORDER BY seqnum, scmsdesc
	";
	$notAnswered = db::execSQL($linkedSQL)->recordCount() + db::execSQL($notLinkedSQL)->recordCount();

	if ($notAnswered == 0) {
		return false;
	} else {
		return true;
	}
?>