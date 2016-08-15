<?php

	Security::init();

	$RefID      = io::geti('RefID');
	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $tsRefID);

	$edit->setSourceTable('webset_tx.std_fie_adaptive', 'stdrefid');

	$edit->SQL = "
		SELECT verbal,
               nonverbal,
               composite,
               sem,
               func_other
		  FROM webset_tx.std_fie_adaptive
		 WHERE stdrefid=$tsRefID
		 AND iepyear=$stdIEPYear
		";

	$edit->title       = "Results and Interpretations";
	$edit->saveAndAdd  = false;
	$edit->saveAndEdit = true;

	$edit->addControl("Verbal  Score", "integer")
		->sqlField('verbal')
		->size(3);

	$edit->addControl("Nonverbal Score", "integer")
		->sqlField('nonverbal')
		->size(3);

	$edit->addControl("Full Scale/Composite Score", "integer")
		->sqlField('composite')
		->size(3);

	$edit->addControl("SEM", "integer")
		->sqlField('sem')
		->size(3);

	$edit->addControl("Other", "textarea")
		->sqlField('func_other')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->finishURL  = 'javascript:parent.parent.selectNext()';

	$edit->printEdit();

?>