<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	$RefID = io::geti('RefID');

	$edit = new EditClass('edit', $RefID);

	$edit->title = 'Add/Edit Justification for Provision of Service in Environments/Settings not Identified as the Natural Environment';
	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addGroup('General');

	$edit->addControl('Service', 'textarea')
		->sqlField('txt01');

	$edit->addControl('Options Considered', 'textarea')
		->sqlField('txt02');

	$edit->addControl('The desired outcome could not be achieved in the natural environment because:', 'textarea')
		->sqlField('txt03');

	$edit->addUpdateInformation();

	$edit->addControl('stdrefid', 'hidden')
		->sqlField('stdrefid')
		->value($tsRefID);

	$edit->addControl('stdIEPYear', 'hidden')
		->sqlField('iepyear')
		->value($stdIEPYear);

	$edit->addControl('area_id', 'hidden')
		->sqlField('area_id')
		->value(IDEAAppArea::TN_IFSP_SEVICES);

	$edit->printEdit();
?>