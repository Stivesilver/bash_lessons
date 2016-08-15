<?php

	Security::init();

	$RefID      = io::geti('RefID');
	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_social', 'refid');

	$edit->title       = "Add/Edit Data";
	$edit->saveAndAdd  = true;
	$edit->saveAndEdit = false;

	$edit->addGroup("General Information");
	$edit->addControl("Source of Data", "textarea")
		->sqlField('s_src')
		->css("width", "100%")
		->css("height", "100px")
		->req(true);

	$edit->addControl("Date:", "date")->sqlField('s_date');

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("", "hidden")->value("8")->sqlField('apptype');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>