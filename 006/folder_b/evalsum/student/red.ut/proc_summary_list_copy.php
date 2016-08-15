<?php

	Security::init();

	$refids = io::get('refids');
	$refids = explode(',', $refids);

	$eprefid = io::get('eprefid', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$evalproc_id = $ds->safeGet('evalproc_id');

	foreach ($refids as $refid) {
		$redinfo = db::execSQL("
			SELECT sesarefid,
				   stdrefid,
				   red_text,
				   lastupdate,
				   lastuser,
				   red_desc,
				   red_asstext,
				   red_assneed,
			  	   plafp,
				   skill,
				   evalproc_id,
				   screening_id
			  FROM webset.es_std_red
			 WHERE redrefid = $refid
		")->assoc();
		$redrefid = DBImportRecord::factory('webset.es_std_red', 'redrefid')
			->key('screening_id', $redinfo['screening_id'])
			->key('evalproc_id', $evalproc_id)
			->set('sesarefid', $redinfo['sesarefid'])
			->set('stdrefid', $redinfo['stdrefid'])
			->set('lastupdate', $redinfo['lastupdate'])
			->set('lastuser', $redinfo['lastuser'])
			->set('red_desc', $redinfo['red_desc'])
			->set('red_text', $redinfo['red_text'])
			->set('red_asstext', $redinfo['red_asstext'])
			->set('red_assneed', $redinfo['red_assneed'])
			->set('plafp', $redinfo['plafp'])
			->set('skill', $redinfo['skill'])
			->import(DBImportRecord::UPDATE_OR_INSERT)
			->recordID();

		$redds = db::execSQL("
			SELECT refid,
				   dsrefid,
				   ds_other,
				   lastupdate,
				   lastuser
			  FROM webset.es_std_redds
			 WHERE redrefid = $refid
		")->assocAll();

		db::execSQL("DELETE FROM webset.es_std_redds WHERE redrefid = $redrefid");

		foreach ($redds as $redd) {
			if ($redd) {
				DBImportRecord::factory('webset.es_std_redds', 'refid')
					->key('redrefid', $redrefid)
					->set('dsrefid', $redd['dsrefid'])
					->set('ds_other', $redd['ds_other'])
					->set('lastupdate', $redd['lastupdate'])
					->set('lastuser', $redd['lastuser'])
					->import(DBImportRecord::UPDATE_OR_INSERT);
			}
		}
	}
?>
