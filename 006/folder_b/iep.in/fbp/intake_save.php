<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	#Texts save
	$texts = db::execSQL("
		SELECT irefid
		  FROM webset.statedef_in_fbp_intakes state
	")->assocAll();

	for ($i = 0; $i < count($texts); $i++) {
		DBImportRecord::factory('webset.std_in_fbp_intakes', 'isrefid')
			->key('stdrefid', $tsRefID)
			->key('tr_item_id', $texts[$i]['irefid'])
			->set('item_text', io::post($texts[$i]['irefid']))
			->set('lastuser', db::escape(SystemCore::$userUID))
			->set('lastupdate', 'NOW()', true)
			->import();
	}

	#Dates save
	DBImportRecord::factory('webset.std_in_fbp_intakes_med', 'imrefid')
		->key('stdrefid', $tsRefID)
		->set('medic', io::post('medic'))
		->set('revdt1', io::post('revdt1'))
		->set('revdt2', io::post('revdt2'))
		->set('revdt3', io::post('revdt3'))
		->set('revdt4', io::post('revdt4'))
		->set('revdt5', io::post('revdt5'))
		->set('revdt6', io::post('revdt6'))
		->set('revdt7', io::post('revdt7'))
		->set('revdt8', io::post('revdt8'))
		->set('revdt9', io::post('revdt9'))
		->set('revdt10', io::post('revdt10'))
		->import();

	if (io::post('finishFlag') == 'yes') {
		io::js('
            var edit1 = EditClass.get(); 
            edit1.cancelEdit();
        ');
	} else {
		io::js('
            var edit1 = EditClass.get(); 
            edit1.reload();
        ');
	}
?>
