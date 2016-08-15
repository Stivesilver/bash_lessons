<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$values = '';
    foreach ($_POST as $key => $val) {
        if ($val != '' && (substr($key, 0, 5) == 'main_' || substr($key, 0, 7) == 'second_') ) {
            $values .= $key . '|' . $val . '!!!';
        }
    }
	
	DBImportRecord::factory('webset_tx.std_lre_statements', 'refid')
		->key('stdrefid', $tsRefID)
		->key('iep_year', $stdIEPYear)
		->key('area', io::get('QuestionID'))
		->set('all_objects', $values)
		->set('lastuser', SystemCore::$userUID)
		->set('lastupdate', 'NOW()', true)
		->import();

	if (io::post('finishFlag') == 'yes') {
		io::js('
            var edit1 = EditClass.get(); 
            edit1.cancelEdit();
        ');
	} else {
		io::js('
            api.reload();
        ');
	}
?>
