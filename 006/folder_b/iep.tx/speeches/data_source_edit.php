<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_adata', 'refid');

	$edit->title = "Add/Edit Sources Of Data";

	$edit->addGroup("General Information");
	$edit->addControl("Source of Data", "textarea")
		->sqlField('s_src')
		->css("width", "100%")
		->css("height", "100px")
		->req();

	$edit->addControl("Date:", "date")->sqlField('s_date');

	$edit->addControl(FFSwitchYN::factory("Report Attached:"))->sqlField('report');

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>