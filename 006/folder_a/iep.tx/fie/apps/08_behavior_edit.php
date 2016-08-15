<?php

	Security::init();

	$RefID      = io::geti('RefID');
	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_adaptivescore', 'adrefid');

	$edit->title = "Add/Edit Score";

	$edit->addGroup("General Information");
	$edit->addControl("Area", "list")
		->sqlField('area_id')
		->sql("
			SELECT refid,
                   validvalue
              FROM webset.glb_validvalues
             WHERE valuename = 'TX_FIE_Adaptives'
               AND (glb_enddate IS NULL or now()< glb_enddate)
               AND refid not in (SELECT area_id
                                   FROM webset_tx.std_fie_adaptivescore
                                  WHERE iepyear = $stdIEPYear
                                 )
           	 ORDER BY sequence_number, validvalue
            ");

	$edit->addControl("Score", "edit")
		->sqlField('score')
		->size(5);

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->printEdit();

?>