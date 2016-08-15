<?php

	Security::init();

	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit TELPAS';

	$edit->setSourceTable('webset_tx.std_sam_taks', 'refid');

	$edit->addGroup('Assessments');

	$edit->addControl('Assessments', 'select_radio')
		->sqlField('assessments')
		->name('assessments')
		->sql("
			SELECT swarefid,
				   swadesc
			  FROM webset.statedef_assess_state
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			   AND swadesc like '%TELPAS%'
			 ORDER BY swaseq, swadesc
		");

	$edit->addControl('Grades', 'select_check')
		->sqlField('grades')
		->name('grades')
		->sql("
			SELECT glrefid,
				   gldesc
				   FROM webset.def_gradelevel
			 WHERE glrefid in (SELECT grade_id
			   				     FROM webset.statedef_assess_links links
									  INNER JOIN webset.statedef_assess_state assess ON assessment_id = swarefid
							    WHERE screfid = " . VNDState::factory()->id . "
							      AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
							      AND swadesc like '%TELPAS%'
							 )
			ORDER BY gldesc
		");

	$edit->addControl('Language', 'select_check')
		->sqlField('languages')
		->name('languages')
		->value(
			db::execSQL("
				SELECT refid
			  FROM webset.statedef_prim_lang
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)	 
			   AND (adesc= 'English')
			")->getOne()
		)
		->sql("
			SELECT refid,
				   adesc
			  FROM webset.statedef_prim_lang
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (adesc= 'English' or adesc= 'Spanish')
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			 ORDER BY adesc
		");

	$edit->addControl('Subject', 'select_radio')
		->sqlField('subjects')
		->name('subjects')
		->sql("
			SELECT aaarefid,
				   aaadesc
			  FROM webset.statedef_assess_acc
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			   AND aaarefid IN (
						SELECT subject_id
						  FROM webset.statedef_assess_links
						 WHERE assessment_id IN (VALUE_01)
						   AND grade_id IN (VALUE_02)
						   AND language_id IN (VALUE_03)
				   )
				   OR aaarefid = 0
			 ORDER BY CASE aaarefid WHEN 0 THEN 'Z' ELSE aaadesc END
		")
		->tie('assessments')
		->tie('grades')
		->tie('languages');
	

	$edit->addControl('Accommodations and/or Modifications', 'textarea')
		->sqlField('accomodation')
		->css('width', '100%')
		->css('height', '50px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('SAM ID', 'hidden')->value($samrefid)->sqlField('samrefid');

	$edit->finishURL = CoreUtils::getURL('telpas_telpop.php', array('dskey' => $dskey, 'samrefid' => $samrefid));
	$edit->cancelURL = CoreUtils::getURL('telpas_telpop.php', array('dskey' => $dskey, 'samrefid' => $samrefid));

	$edit->printEdit();
?>