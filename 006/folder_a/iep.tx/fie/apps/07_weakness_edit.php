<?php

	Security::init();

	$area       = 2;
	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);
	$SQL        = "
		SELECT a_name
          FROM webset_tx.def_fie_academic
         WHERE a_refid = $area
        ";

	$area = db::execSQL($SQL)->getOne();

	$edit->setSourceTable('webset_tx.std_fie_academic', 'refid');

	$edit->title       = "Add/Edit $area Strengths/Weaknesses";
	$edit->saveAndAdd  = true;
	$edit->saveAndEdit = false;

	$edit->addGroup("General Information");
	$edit->addControl("Strengths", "textarea")
		->sqlField('strength')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addControl("Weaknesses", "textarea")
		->sqlField('weakness')
		->css("width", "100%")
		->css("height", "100px");

	$edit->addControl("stdrefid", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("iepyear", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->addControl("Area", "hidden")
		->value(2)
		->sqlField('a_refid');

	$edit->printEdit();

?>