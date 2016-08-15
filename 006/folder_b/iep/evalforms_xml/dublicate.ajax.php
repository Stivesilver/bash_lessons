<?php
	Security::init();

	$RefIDs = explode(',', io::post('RefID'));

	foreach ($RefIDs as $item) {
		DBCopyRecord::factory('webset.std_forms_xml', 'sfrefid')
			->key('sfrefid', $item)
			->set('lastupdate', date('Y-m-d H:i:s'))
			->set('lastuser', SystemCore::$userUID)
			->copyRecord();
	}

?>
