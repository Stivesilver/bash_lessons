<?php

	Security::init();

	$res = io::get('res', false);
	$recs_num = io::get('recs_num', false);

	db::beginTrans();

	for ($i = 0; $i < $recs_num; $i++) {
		$newRefID = DBCopyRecord::factory('webset.std_bgb_measurement', 'm_refid')
			->key('m_refid', $res)
			->set('lastupdate', date('m-d-Y H:i:s'))
			->set('lastuser', SystemCore::$userUID)
			->set('vndrefid', SystemCore::$VndRefID)
			->copyRecord()
			->recordID();

		$mi_refids = db::execSQL("
			SELECT mi_refid
			  FROM webset.std_bgb_measurement_indicator
			 WHERE m_refid = $res
		")
		->indexCol();

		foreach ($mi_refids as $key => $mi_refid) {
			DBCopyRecord::factory('webset.std_bgb_measurement_indicator', 'mi_refid')
				->key('mi_refid', $mi_refid)
				->set('m_refid', $newRefID)
				->set('lastupdate', date('m-d-Y H:i:s'))
				->set('lastuser', SystemCore::$userUID)
				->copyRecord();
		}
	}

	db::commitTrans();
?>
