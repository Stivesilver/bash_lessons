<?php
	Security::init();

	$table = io::get('table');
	$key_field = io::get('key_field');
	$cont_field = io::get('cont_field');
	$keys = json_decode(io::get('keys'));

	$RefIDs = explode(',', io::post('RefID'));
	for ($i = 0; $i < sizeOf($RefIDs); $i++) {
		if ($RefIDs[$i] > 0) {
			foreach (explode(',', io::get('forms')) as $form_id) {
				if ($form_id > 0) {
					$form = db::execSQL("
						SELECT $cont_field,
							   " . implode(',', $keys) ."
						  FROM $table
						 WHERE $key_field = " . $form_id . "
					")->assoc();
					$import = DBImportRecord::factory($table, $key_field)
						->key('vndrefid', $RefIDs[$i])
						->set($cont_field, $form[$cont_field]);
					foreach ($keys AS $key) {
						$import->key($key, $form[$key]);
					}
					$import->set('lastuser', db::escape(SystemCore::$userUID))
						->set('lastupdate', 'NOW()', true);
					$import->import();
				}
			}
		}
	}
?>
