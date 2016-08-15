<?php
	Security::init();

    $dskey     = io::get('dskey');
    $tsRefID   = DataStorage::factory($dskey)->safeGet('tsRefID');
    $student   = IDEAStudent::factory($tsRefID);

	$RefIDs = explode(',', io::post('RefID'));
	for ($i=0; $i < sizeOf($RefIDs); $i++) {
		if($RefIDs[$i]>0) {
            if ($RefIDs[$i]==$student->get('stdiepyear')) {
                io::msg('Current IEP Year can not be deleted.', false);
            } else {
                DBImportRecord::factory('webset.std_iep_year', 'siymrefid')
                    ->key('siymrefid', $RefIDs[$i])
                    ->set('stdrefid', null)
                    ->set('dsyrefid', 'stdrefid', true)
                    ->set('lastuser', db::escape(SystemCore::$userUID))
                    ->set('lastupdate', 'NOW()', true)
                    ->import();
            }
		}
    }
?>
