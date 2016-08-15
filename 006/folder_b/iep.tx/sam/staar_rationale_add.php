<?php

	Security::init();

	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	$assess = io::get('assess');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit ' . $assess . ' Rationale';

	$edit->setSourceTable('webset_tx.std_sam_taks_ratio', 'refid');

	$edit->addGroup('Assessments');

	$edit->addControl('Subject', 'select_radio')
		->sqlField('subject_id')
		->name('subject_id')
		->sql("
			SELECT refid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TX_Rat_Subjects'
			   AND (glb_enddate IS NULL or now()< glb_enddate)
			   AND COALESCE(validvalueid, '" . $assess . "') = '" . $assess . "'
			 ORDER BY valuename, sequence_number
		")
		->breakRow();

	$edit->addControl('Rationale', 'select_radio')
		->sqlField('reationale_id')
		->name('reationale_id')
		->sql("
			SELECT refid,
				   validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TX_Rat_Reasons'
			   AND (glb_enddate IS NULL or now()< glb_enddate)
			   AND COALESCE(validvalueid, '" . $assess . "') = '" . $assess . "'
			 ORDER BY valuename, sequence_number
		");

	$edit->addControl('Other Rationale', 'textarea')
		->sqlField('rationale')
		->css('width', '100%')
		->css('height', '50px')
		->showIf('reationale_id', db::execSQL("
			SELECT refid
			  FROM webset.glb_validvalues
			 WHERE valuename = 'TX_Rat_Reasons'
			   AND (glb_enddate IS NULL or now()< glb_enddate)
			   AND COALESCE(validvalueid, '" . $assess . "') = '" . $assess . "'
			   AND SUBSTRING(LOWER(validvalue), 1, 5) = 'other'
		")->indexAll());

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('SAM ID', 'hidden')->value($samrefid)->sqlField('samrefid');

	$edit->finishURL = CoreUtils::getURL('staar_rationale.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));
	$edit->cancelURL = CoreUtils::getURL('staar_rationale.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));

	$edit->printEdit();
?>