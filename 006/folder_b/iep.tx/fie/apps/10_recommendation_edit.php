<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_recommendation', 'refid');

	$edit->title = "Add/Edit Recommendation";

	$edit->addGroup("General Information");

	$edit->addControl("Recommendation", "select")
		->sql("
			SELECT r_refid,
				   r_name
              FROM webset_tx.def_fie_recommendation
             WHERE r_refid NOT IN (
             						  SELECT r_refid
                                  		FROM webset_tx.std_fie_recommendation
                                  	   WHERE stdrefid = $tsRefID
                                  	     AND iepyear = $stdIEPYear
                                   )
             ORDER BY r_seq, r_name
           ")
		->name('r_refid')
		->sqlField('r_refid');

	$edit->addControl("Other", "edit")
		->sqlField('other')
		->showIf('r_refid', '22')
		->size(75);

	$edit->addUpdateInformation();

	$edit->addControl("stdrefid", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("iepyear", "hidden")->value($stdIEPYear)->sqlField('iepyear');

	$edit->printEdit();