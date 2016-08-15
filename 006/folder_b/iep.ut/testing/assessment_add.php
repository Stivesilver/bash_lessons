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

	$edit->setSourceTable('webset.std_assess_state', 'sswarefid');

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
		")
		->req();

	$edit->addControl('', 'protected')
		->showIf(
			'assessments',
			db::execSQL("
				SELECT swarefid
				  FROM webset.statedef_assess_state
				 WHERE screfid = " . VNDState::factory()->id . "
				   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
				   AND swadefaultnarr = 'A'
			")->indexAll()
		)
		->append(
			UIMessage::factory()
				->textAlign('left')
				->width('100%')
				->type(UIMessage::NOTE)
				->message('
					<b>Participation in Statewide and District-wide Assessment: Include a statement of why the student cannot participate in the regular assessment and why the particular alternate assessment selected is appropriate for the student.</b><br/><br/>
					Sample of language needed for a student who qualifies for a DLM/UAA: STUDENT’S NAME demonstrates cognitive ability (IQ TEST NAME, DATE OF TEST, RESULTS-SCORES) and adaptive skill levels (SIB-R, DATE OF TEST, RESULTS-SCORES) that prevent completion of the general academic core curriculum, even with instructional accommodations. HE/SHE requires extensive individualized instruction in multiple settings to transfer and generalize skills, and is unable to participate in any other component of the statewide assessment system, even with test accommodations.
				')
		);

	$edit->addControl('Other')
		->name('other')
		->css('width', '30%')
		->sqlField('other')
		->showIf(
			'assessments',
			db::execSQL("
				SELECT swarefid
				  FROM webset.statedef_assess_state
				 WHERE screfid = " . VNDState::factory()->id . "
				   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
				   AND LOWER(swadesc) LIKE '%other%'
			")->indexAll())
		->tie('assessments');

	$edit->addControl('Grades', 'select_radio')
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
		")
		->req();

	$edit->addControl(FFCheckBoxList::factory('Subject'))
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
				   )
				   OR aaarefid = 0
			 ORDER BY CASE aaarefid WHEN 0 THEN 'Z' ELSE aaadesc END
		")
		->tie('assessments')
		->tie('grades')
		->req();

	$edit->addControl('Participation Code', 'select')
		->sqlField('partcode')
		->name('partcode')
		->sql("
			SELECT validvalueid,
			       validvalueid || ' - ' || validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'UT_Assess_Part'
			   AND (CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = '1'
			 ORDER BY valuename, sequence_number, validvalue ASC
        ")
		->req();

	$edit->addControl('Comments' , 'textarea')
		->sqlField('na_reason')
		->css('width', '100%')
		->css('height', '50px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Student IEP Year', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->finishURL = CoreUtils::getURL('assessment_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('assessment_list.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
