<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_assurance', 'refid');

	$edit->title = "Add/Edit Assurance";

	$edit->addGroup("General Information");
	$edit->addControl("Participant Name", "edit")
		->sqlField('name')
		->size(75);

	$edit->addControl("Position", "edit")
		->sqlField('position')
		->size(75);

	$edit->addControl("stdrefid", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("iepyear", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>