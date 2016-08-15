<?php

	function dagasources($RefID, &$data) {
		$RefIDs = explode(',', io::post('datasource'));
		$SQL = "
            DELETE FROM webset.es_std_redds WHERE redrefid = $RefID
        ";
		db::execSQL($SQL);
		for ($i = 0; $i < sizeOf($RefIDs); $i++) {
			if ($RefIDs[$i] > 0) {
				DBImportRecord::factory('webset.es_std_redds', 'refid')
					->key('dsrefid', $RefIDs[$i])
					->key('redrefid', $RefID)
					->set('ds_other', io::post('datasource_other'))
					->set('lastuser', db::escape(SystemCore::$userUID))
					->set('lastupdate', 'NOW()', true)
					->import();
			}
		}
	}

?>

