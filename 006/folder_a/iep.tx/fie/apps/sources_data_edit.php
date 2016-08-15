<?php

	Security::init();

	$dskey         = io::get('dskey');
	$RefID         = io::get('RefID');
	$ds            = DataStorage::factory($dskey, true);
	$stdSchoolYear = $ds->safeGet('stdIEPYear');
	$tsRefID       = $ds->get('tsRefID');

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_social', 'refid');

	$edit->title       = "Add/Edit Data";
	$edit->saveAndAdd  = false;
	$edit->saveAndEdit = true;

	$edit->addGroup("General Information");
	$edit->addControl("Source of Data", "textarea")
		->sqlField('s_src')
		->css("width", "100%")
		->css("height", "100px")
		->req();

	$edit->addControl("Date:", "date")->sqlField('s_date');

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdSchoolYear)
		->sqlField('iepyear');

	$edit->addControl("", "hidden")
		->value("1")
		->sqlField('apptype');

	$edit->printEdit();

?>
