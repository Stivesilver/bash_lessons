<?php

	Security::init();

	$area       = 1;
	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_academic', 'refid');

	$edit->title = "Add/Edit $area Strengths/Weaknesses";

	$edit->addGroup("General Information");
	$edit->addControl("Strengths", "textarea")
		->sqlField('strength')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addControl("Weaknesses", "textarea")
		->sqlField('weakness')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addControl("stdrefid", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("iepyear", "hidden")->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("Area", "hidden")->value($area)->sqlField('a_refid');

	$edit->printEdit();