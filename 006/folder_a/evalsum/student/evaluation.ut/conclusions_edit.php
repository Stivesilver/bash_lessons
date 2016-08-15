<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass('edit1', $evalproc_id);

	$edit->title = 'Team Conclusions and Decisions';
	$edit->topButtons = true;

	$edit->setSourceTable('webset.es_std_er_conclusions', 'eprefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFIDEASwitchYN::factory('The student was assessed in all areas related to the suspected disability, including, if appropriate, health, vision, hearing, social/emotional status, general intelligence, academic performance, communication, and motor abilities.'))
		->data(array(
			'N' => 'No (If no, the evaluation is not sufficiently comprehensive and the evaluation is incomplete.)',
			'Y' => 'Yes'
		))
		->breakRow()
		->sqlField('was_assess_sw');

	$edit->addControl(FFIDEASwitchYN::factory('There is documentation to confirm this student has a disability under the IDEA?'))
		->data(array(
			'N' => 'No',
			'Y' => 'Yes'
		))
		->name('disability_confirmed_sw')
		->sqlField('disability_confirmed_sw');

	$edit->addControl(FFSelect::factory("If yes, list eligibility category"))
		->sql("
			SELECT elrefid,
			       eldesc
			  FROM webset.es_statedef_eligibility AS t
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND COALESCE(recdeactivationdt, now()) >= now()
			 ORDER BY seqnum, elcode, eldesc
		")
		->emptyOption(true)
		->showIf('disability_confirmed_sw', 'Y')
		->name('disability_id')
		->sqlField('disability_id');

	$edit->addControl(FFCheckBoxList::factory("Subcategory (if appropriate)"))
		->sqlField('disability_text')
		->displaySelectAllButton(false)
		->breakRow(true)
		->sql("
			SELECT elsrefid, elsdesc
			  FROM webset.es_statedef_eligibility_sub
			 WHERE elrefid = VALUE_01
		")
		->showIf('disability_id', db::execSQL("
                                  SELECT elrefid
                                    FROM webset.es_statedef_eligibility_sub
                                 ")->indexAll()
		)
		->tie('disability_id');

	$edit->addControl(FFIDEASwitchYN::factory('Does this disability adversely affect the student\'s education?'))
		->data(array(
			'N' => 'No',
			'Y' => 'Yes'
		))
		->sqlField('disability_affect_sw');

	$edit->addControl(FFIDEASwitchYN::factory('Does the student need specially designed instruction?'))
		->data(array(
			'N' => 'No',
			'Y' => 'Yes'
		))
		->sqlField('sped_needed_sw');

	$edit->addGroup('IF ELIGIBLE, THIS EVALUATION REPORT REFLECTS THAT THE CHILD\'S ELIGIBILITY DETERMINATION WAS NOT BASED ON ANY OF THE FOLLOWING FACTORS');

	$edit->addControl(FFCheckBox::factory('A lack of appropriate instruction in reading including the essential components of reading instruction'))
		->baseValue('Y')
		->sqlField('lack_instruction_read_sw');

	$edit->addControl(FFCheckBox::factory('A lack of appropriate instruction in math'))
		->baseValue('Y')
		->sqlField('lack_instruction_math_sw');

	$edit->addControl(FFCheckBox::factory('Limited English Proficiency'))
		->baseValue('Y')
		->sqlField('lep_sw');

	$edit->addControl(FFCheckBoxList::factory('Describe any other exclusionary factors  relevant to the eligibility category'))
		->name('other_factors_sw')
		->displaySelectAllButton(false)
		->data(array(
			1 => ''
		))
		->sqlField('other_factors_sw')
		->append(
			FFRadioList::factory()
			->showIf('disability_id', json_decode(IDEAFormat::getIniOptions('mo_er_eligibility_other')))
			->append(UIMessage::factory('Make sure to set this check box for selected Eligibility', UIMessage::NOTE)->toHTML())
		);

	$edit->addControl('Describe', 'textarea')
		->sqlField('other_factors_text')
		->showIf('other_factors_sw', 1);

	$edit->addGroup('RELEVANT MEDICAL FINDINGS');

	$edit->addControl(FFCheckBox::factory('There are no relevant medical findings.'))
		->baseValue('Y')
		->sqlField('medical_finding_no_sw');

	$edit->addControl(FFCheckBoxList::factory('Relevant medical findings are'))
		->name('medical_finding_yes_sw')
		->displaySelectAllButton(false)
		->data(array(
			1 => ''
		))
		->sqlField('medical_finding_yes_sw');

	$edit->addControl('', 'textarea')
		->sqlField('medical_finding_are')
		->showIf('medical_finding_yes_sw', 1);

	$edit->addControl('If not eligible for special education and related services OR the student does not need specially designed instruction, suggestions for interventions for the student', 'textarea')
		->sqlField('suggestions');

	$edit->addUpdateInformation();
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('', array('dskey' => $dskey));
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '40%';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_er_conclusions')
			->setKeyField('eprefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
