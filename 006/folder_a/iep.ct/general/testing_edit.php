<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudentCT($tsRefID);

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'State and District Testing and Accommodations';

	$edit->saveLocal = false;
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$data = $student->getTesting(1);

	$edit->firstCellWidth = '40%';

	$edit->addTab('CMT/CAPT');
	$edit->addGroup('Grade');

	$edit->addControl(FFIDEAGradeLevel::factory())
		->caption('Check the grade the student will be in when the test is given.')
		->name('grade')
		->emptyOption(true)
		->value(!isset($data['grade']) ? $student->get('grdlevel_id') : $data['grade']);

	$edit->addGroup('Assessment Options: (Select Only ONE Option.)');

	$edit->addControl('Standard Assessments and Alternate Assessment', 'select_radio')
		->value($data['assessment'])
		->name('assessment')
		->sql(IDEADef::getValidValueSql("CT_Assessment_Options", "refid, validvalueid || '. ' ||validvalue"))
		->help('Smarter Balanced Assessments; Connecticut SAT and the CTAA include English Language Arts and Mathematics. ALL students in grades 5 & 8 will also take the CMT Science Test or CMT Skills Checklist Science. Students in Grade 10 will ONLY take the CAPT Science or CAPT Skills Checklist Science.')
		->breakRow();

	$edit->addControl('CAPT', 'select_radio')
		->name('mas')
		->enabledIf('assessment', 924)
		->value($data['mas'])
		->sql(IDEADef::getValidValueSql("CT_Assessment_MAS", "refid, validvalue"))
		->breakRow();

	$edit->addGroup('Administration Options: (Select Only ONE Option.) Accommodations will be provided.');

	$edit->addControl('The student is participating in the Smarter Balanced Assessments or CAPT Science and requires designated supports and/or accommodations**', 'select_check')
		->name('accommodations_prov')
		->value($data['accommodations_prov'])
		->data(array('Y' => ''))
		->displaySelectAllButton(false);

	$edit->addControl('The student is participating in the Connecticut SAT and will request accommodations***', 'select_check')
		->name('ell')
		->value($data['ell'])
		->data(array('Y' => ''))
		->displaySelectAllButton(false);

	$edit->addTab('Districtwide Assessments');

	$edit->addGroup('Grade');

	$grades = db::execSQL('
	   SELECT gl_refid,
              gl_code
         FROM c_manager.def_grade_levels
        WHERE vndrefid = VNDREFID
        ORDER BY gl_numeric_value, gl_code'
	)->assocAll();
	$gradesArr = array();
	foreach ($grades as $grade) {
		$gradesArr[$grade['gl_refid']] = $grade['gl_code'];
	}

	$edit->addControl(FFMultiSelect::factory('Districtwide Grades'))
		->value(!isset($data['grade_districtwide']) ? $student->get('grdlevel_id') : $data['grade_districtwide'])
		->name('grade_districtwide')
		->data($gradesArr);

	$edit->addGroup('Districtwide Assessmnets');
	$edit->addControl('N/A - No districtwide assessments are scheduled during the term of this IEP.', 'select_check')
		->name('distr_assessment_na')
		->value($data['distr_assessment_na'])
		->data(array('Y' => ''))
		->displaySelectAllButton(FALSE);

	$edit->addControl('Alternate Assessment(s)', 'select_check')
		->name('distr_assessment')
		->value($data['distr_assessment'])
		->data(array('N' => ''))
		->displaySelectAllButton(FALSE);

	$edit->addControl('Specify', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('aleternativ_assessment')
		->showIf('distr_assessment', 'N')
		->help('Alternate assessments must be specified and a statement provided for each as to why the child cannot participate in the standard assessment and why the particular alternate assessment selected is appropriate for the child.')
		->value($data['aleternativ_assessment']);

	$edit->addControl(
		FFSwitchYN::factory('Select one of the following options')
			->data(
				array(
					'A' => 'No accommodations will be provided, OR',
					'Y' => 'Accommodations will be provided as specified on Page 8, OR',
					'N' => 'Accommodations will be provided as specified below.'))
			->name('distr_accommodation')
			->value($data['distr_accommodation'])
			->breakRow()
	);

	$edit->addControl('Accommodations', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('aleternativ_accommodation')
		->showIf('distr_accommodation', 'N')
		->value($data['aleternativ_accommodation']);

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_assess_state')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->setPresaveCallback('saveData', 'testing_edit.inc.php', array('mode' => 1, 'stdrefid' => $tsRefID, 'iepyear' => $stdIEPYear));
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->printEdit();

	$message = "
		* <i>CTAA for grades 3-8 & 11 and CMT/CAPT Science Skills Checklists Eligibility & Learner Characteristics Inventory (LCI)</i> should be used for guidance on eligibility requirements. Provide a completed copy of the LCI to the district test coordinator for required registration of students assessed with the CT Alternate Assessment (CTAA) and the CMT/CAPT Science Skills Checklists. <b>A PPT decision to assess the student using the CTAA and/or the CMT/CAPT Science Skills Checklists must be recorded on page 3 of the IEP, Prior Written Notice.</b>
		<br/>
		<br/>
		** If accommodations are given, attach a copy of the Test Supports/Accommodations Form to the IEP and provide a copy to the district test coordinator for required registration.
		<br/>
		<br/>
		*** <b>Please note</b>: There are two options for requesting accommodations. One option is through the <b>College Board (CB) process</b>: If all accommodations are approved through the CB process, test scores can be used for college admission and state accountability. The other option is through the <b>State Allowed Accommodations (SAA) process</b>: If accommodations are approved through the SAA process, test scores can ONLY be used for state accountability and NOT for college admission. <b>Please make sure to discuss these options at a PPT meeting before completing this page of the IEP.</b>
		* ";

	print UIMessage::factory($message, UIMessage::NOTE)
		->textAlign('left')
		->toHTML();

?>
