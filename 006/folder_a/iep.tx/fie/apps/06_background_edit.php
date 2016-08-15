<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_bground', 'refid');

	$edit->title = "Add/Edit Cultural, Linguistic, and Experiential Background";

	$edit->addGroup("General Information");

	$edit->addControl("Background", "select")
		->sql("
			SELECT b_refid, b_name
              FROM webset_tx.def_fie_bground
             ORDER BY b_seq, b_name
        ")
		->sqlField('b_refid')
		->name('b_refid');

	$edit->addControl("Other", "edit")
		->sqlField('other')
		->showIf('b_refid', '6')
		->size(71);

	$edit->addUpdateInformation();

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