<?php
	Security::init();
	$RefIDs = explode(',', io::post('RefID'));
	for ($i=0; $i < sizeOf($RefIDs); $i++) {
		if ($RefIDs[$i] > 0) {              
			foreach (explode(',', io::get('forms')) as $form_id) {
				if ($form_id > 0) {              
					$form = db::execSQL("
						SELECT hspdesc, 
						       xml_test,
							   screenid
						  FROM webset.es_scr_disdef_proc
						 WHERE hsprefid = " . $form_id . "
					")->assoc();
					DBImportRecord::factory('webset.es_scr_disdef_proc', 'hsprefid')
						->key('vndrefid', $RefIDs[$i])
						->key('hspdesc', $form['hspdesc'])
						->key('screenid', $form['screenid'])
						->set('xml_test', $form['xml_test'])
						->set('lastuser', db::escape(SystemCore::$userUID))
						->set('lastupdate', 'NOW()', true)
						->import();
				}
			}
		}
	}
?>
