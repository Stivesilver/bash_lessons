<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_social', 'refid');

	$edit->title       = "Add/Edit Social/Emotional";
	$edit->saveAndAdd  = true;
	$edit->saveAndEdit = false;

	$edit->addGroup("General Information");
	$edit->addControl("Source of Data", "textarea")
		->sqlField('s_src')
		->css("width", "100%")
		->css("height", "100px")
		->req();

	$edit->addControl("Date", "date")->sqlField('s_date');

	$edit->addUpdateInformation();

	$edit->addControl("stdrefid", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("iepyear", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addControl("apptype", "hidden")
		->value(7)
		->sqlField('apptype');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>