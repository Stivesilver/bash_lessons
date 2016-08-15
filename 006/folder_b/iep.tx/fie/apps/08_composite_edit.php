<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $tsRefID);

	$edit->title       = 'Composite Score';
	$edit->saveAndAdd  = false;
	$edit->saveAndEdit = true;

	$edit->setSourceTable('webset_tx.std_fie_adaptive', 'stdrefid');

	$edit->addControl("Composite Score", "textarea")
		->sqlField('composite1')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl(
		FFSwitchYN::factory(
			"This student's level of intellectual functioning is consistent with his/her adaptive behavior"
		)
	)
	->sqlField('level');

	$edit->addControl("If no, check applicable", "select_check")
		->sqlField('noneareas')
		->sql("
			SELECT refid,
                   validvalue
              FROM webset.glb_validvalues
             WHERE valuename = 'TX_FIE_Adaptives'
               AND (glb_enddate IS NULL or now()< glb_enddate)
             ORDER BY sequence_number, validvalue
            ")
		->breakRow();

	$edit->saveLocal      = false;
	$edit->firstCellWidth = "40%";
	$edit->finishURL      = 'javascript:parent.parent.selectNext()';
	$edit->saveAndAdd     = false;

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>