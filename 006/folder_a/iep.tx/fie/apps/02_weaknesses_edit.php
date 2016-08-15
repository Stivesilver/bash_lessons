<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::get('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$editUrl    = CoreUtils::getURL('weaknesses_edit.php', array('dskey' => $dskey));
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$area       = db::execSQL("
		SELECT a_name
          FROM webset_tx.def_fie_academic
         WHERE a_refid = 3
	")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_academic', 'refid');

	$edit->title       = "Add/Edit $area Strengths/Weaknesses";
	$edit->saveAndAdd  = true;
	$edit->saveAndEdit = true;

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
		->value(3)
		->sqlField('a_refid');

	$edit->printEdit();

?>