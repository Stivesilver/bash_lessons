<?php

	Security::init();

	$dskey = io::get('dskey');
	$samrefid = io::geti('samrefid');
	$assess = io::get('assess');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Subjects and Accommodations';

	$edit->setSourceTable('webset_tx.std_sam_taks', 'refid');

	$edit->addGroup('Assessments');

	$edit->addControl('Assessments', 'select')
		->sqlField('assessments')
		->name('assessments')
		->sql("
			SELECT swarefid,
				   swadesc
			  FROM webset.statedef_assess_state
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			   AND swadesc like '%" . $assess . "%'
			 ORDER BY swaseq, swadesc
		");


	$edit->addControl('Other')
		->name('other')
		->css('width', '30%')
		->sqlField('other')
		->showIf('assessments', db::execSQL("
			SELECT swarefid
			  FROM webset.statedef_assess_state
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
			   AND LOWER(swadesc) LIKE '%other%'
                ")->indexAll())
		->tie('assessments');

	$edit->addControl('Grades', 'select')
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
							      AND swadesc like '%" . $assess . "%'
							 )
			ORDER BY gldesc
		");

	$edit->addControl('Language', 'select')
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

	$edit->addControl('Subject', 'select')
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
						 WHERE assessment_id IN (NULLIF('VALUE_01','')::integer)
						   AND grade_id IN (NULLIF('VALUE_02','')::integer)
						   AND language_id IN (NULLIF('VALUE_03','')::integer)
				   )
			 ORDER BY CASE aaarefid WHEN 0 THEN 'Z' ELSE aaadesc END
		")
		->tie('assessments')
		->tie('grades')
		->tie('languages');


	$edit->addControl('Other Subject')
		->sqlField('subject_oth')
		->showIf('subjects', 195);

	if ($assess == 'TAKS') {

		$edit->addControl('Accommodations and/or Modifications', 'select_check')
			->sqlField('ids_accommodations')
			->sql("
				SELECT stsrefid,
					   stsdesc
				  FROM webset.statedef_mod_acc
				 WHERE screfid = " . VNDState::factory()->id . "
				   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
				   AND ',' || ids_assessments || ',' LIKE '%,VALUE_01,%'
				   AND ',' || allowedgrades || ',' LIKE '%,VALUE_02,%'
				   AND id_subject IN (VALUE_03)
				 ORDER BY stsseq, stsdesc
			")
			->tie('assessments')
			->tie('grades')
			->tie('subjects')
			->breakRow();
	}

	$edit->addControl($assess == 'TAKS' ? 'Other Accommodations' : 'Accommodations' , 'textarea')
		->sqlField('accomodation')
		->css('width', '100%')
		->css('height', '50px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('SAM ID', 'hidden')->value($samrefid)->sqlField('samrefid');

	$edit->finishURL = CoreUtils::getURL('staar_subject.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));
	$edit->cancelURL = CoreUtils::getURL('staar_subject.php', array('dskey' => $dskey, 'samrefid' => $samrefid, 'assess' => $assess));

	$edit->printEdit();
?>
