<?php

	Security::init();

	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	$assess = io::get('assess');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit STAAR Student Success Initiative';

	$edit->setSourceTable('webset_tx.std_sam_taks_success', 'refid');

	$edit->addGroup('Assessments');

	$edit->addControl('Assessment', 'select_radio')
		->sqlField('assessment_id')
		->name('assessment_id')
		->sql("
			SELECT swarefid,
				   swadesc
			  FROM webset.statedef_assess_state
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			   AND (swadesc = '" . $assess . "' or swadesc = '" . $assess . "-M' or swadesc = '" . $assess . " A')
			 ORDER BY swaseq, swadesc
		");

	$edit->addControl('Subject', 'select_radio')
		->sqlField('subject_id')
		->name('subject_id')
		->sql("
			SELECT aaarefid,
				   aaadesc
			  FROM webset.statedef_assess_acc
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			   AND aaadesc in ('Reading', 'Math')
			 ORDER BY CASE aaarefid WHEN 0 THEN 'Z' ELSE aaadesc END
		");

	$edit->addControl('Grade', 'select_radio')
		->sqlField('grade_id')
		->name('grade_id')
		->sql("
			SELECT glrefid,
				   gldesc
			  FROM webset.def_gradelevel
			 WHERE gldesc in ('05', '08')
			 ORDER BY gldesc
		");

	$edit->addControl('Accelerated Improvement Plan 1', 'textarea')
		->sqlField('plan1')
		->css('width', '100%')
		->css('height', '50px');

	$edit->addControl('Accelerated Improvement Plan 2', 'textarea')
		->sqlField('plan2')
		->css('width', '100%')
		->css('height', '50px');

	$edit->addControl('Accelerated Improvement Plan 3', 'textarea')
		->sqlField('plan3')
		->css('width', '100%')
		->css('height', '50px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('SAM ID', 'hidden')->value($samrefid)->sqlField('samrefid');

	$edit->finishURL = CoreUtils::getURL('staar_initiative.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));
	$edit->cancelURL = CoreUtils::getURL('staar_initiative.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));

	$edit->printEdit();
?>
