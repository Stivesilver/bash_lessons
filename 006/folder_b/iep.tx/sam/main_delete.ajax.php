<?php

	Security::init();

	$RefIDs = explode(',', io::post('RefID'));
	for ($i = 0; $i < sizeOf($RefIDs); $i++) {
		$sam = db::execSQL("
			SELECT TO_CHAR(begdate, 'mm-dd-yyyy') || ' - ' || samdesc as sam,
			       ardinclude
              FROM webset_tx.std_sam_main
  	         WHERE samrefid = " . $RefIDs[$i] . "
		")->assoc();

		if ($RefIDs[$i] > 0) {
			if ($sam['ardinclude'] == 'Y') {
				io::msg($sam['sam'] . ' was not deleted because it is included in ARD', false);
			} else {
				DBImportRecord::factory('webset_tx.std_sam_main', 'samrefid')
					->key('samrefid', $RefIDs[$i])
					->set('stdrefid', null)
					->set('stdrefid_del', 'stdrefid', true)
					->set('lastuser', SystemCore::$userUID)
					->set('lastupdate', 'NOW()', true)
					->import();
			}
		}
	}
?>