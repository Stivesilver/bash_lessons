<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset_tx.std_sat_beh_prog', 'brefid');

	$edit->title = "Add/Edit Campus Rules";

	$edit->addGroup("General Information");
	$edit->addControl("Rule", "textarea")
		->sqlField('prule')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Positive Responses", "textarea")
		->sqlField('responce_pos')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Corrective Responses", "textarea")
		->sqlField('responce_cor')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Start Date:", "date")->sqlField('date_beg');
	$edit->addControl("Beg Date:", "date")->sqlField('date_end');
	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("", "hidden")->value(2)->sqlField('area');
	$edit->printEdit();

?>