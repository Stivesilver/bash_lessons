<?php

	Security::init();

	$dskey   = io::post('dskey');
	$ds      = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	$ds->set('stdIEPYear', $student->get('stdiepyear'))->save(true);

?>