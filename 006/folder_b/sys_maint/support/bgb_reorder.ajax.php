<?php
	Security::init();

	$refids = io::get('refids');

	$refids = explode(',', $refids);

	foreach ($refids as $refid) {

		$iepyears = db::execSQL("
			SELECT siymrefid
		      FROM webset.std_iep_year
			 WHERE stdrefid = " . $refid . "
	         ORDER BY siymiepbegdate DESC
		")->assocAll();

		foreach ($iepyears as $iepyear) {

			$dskey = FFIDEAActionButton::factory()
				->setSeqTable('webset.std_bgb_baseline', 'blrefid', 'order_num')
				->key('stdrefid', $refid)
				->key('siymrefid', $iepyear['siymrefid'])
				->key('esy', 'Y')
				->setNestingSeq('webset.std_bgb_goal', 'grefid', 'order_num', 'blrefid', 'webset.std_bgb_baseline', 'blrefid')
				->setNestingSeq('webset.std_bgb_benchmark', 'brefid', 'order_num', 'grefid', 'webset.std_bgb_goal', 'grefid')
				->reorder();

			$dskey = FFIDEAActionButton::factory()
				->setSeqTable('webset.std_bgb_baseline', 'blrefid', 'order_num')
				->key('stdrefid', $refid)
				->key('siymrefid', $iepyear['siymrefid'])
				->key('esy', 'N')
				->setNestingSeq('webset.std_bgb_goal', 'grefid', 'order_num', 'blrefid', 'webset.std_bgb_baseline', 'blrefid')
				->setNestingSeq('webset.std_bgb_benchmark', 'brefid', 'order_num', 'grefid', 'webset.std_bgb_goal', 'grefid')
				->reorder();
		}
	}

?>
