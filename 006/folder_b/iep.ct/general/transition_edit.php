<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudentCT($tsRefID);

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Transition Planning';

	$edit->saveLocal = false;
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$data = $student->getTransition();

	$edit->firstCellWidth = '50%';
	$edit->setSourceTable('webset.std_in_ts', 'iepyear');

	$edit->addTab('General');
	$edit->addGroup('1. General Information');

	$edit->addControl('Age', 'select_radio')
		->data(
			array(
				'N' => 'Not Applicable: Student has not reached the age of 15 and transition planning is not required or appropriate at this time.',
				'Y' => 'This is either the first IEP to be in effect when the student turns 16 (or younger if appropriate and transition planning is needed) or the student is 16 or older and transition planning is required.'
			)
		)
		->breakRow(true)
		->name('stdage')
		->value($data['stdage'])
		->req();

	$edit->addGroup('2. Student Preferences/Interests');
	$edit->addControl(
		FFSwitchYN::factory('Was the student invited to attend her/his Planning and Placement Team (PPT) meeting?')
			->emptyOption(true)
			->name('invited')
			->value($data['invited'])
	);

	$edit->addControl(
		FFSwitchYN::factory('Did the student attend?')
			->emptyOption(true)
			->name('attended')
			->value($data['attended'])
	);

	$edit->addControl(FFIDEAValidValues::factory('CT_Transition_Interest'))
		->caption('How were the student\'s preferences/interests, as they relate to planning for transition services, determined?')
		->name('interests')
		->value($data['interests']);

	$edit->addControl('Specify')
		->name('other')
		->showIf('interests', db::execSQL(IDEADef::getValidValueSql(array("CT_Transition_Interest", "validvalue ILIKE 'Other'"), 'refid'))->indexAll())
		->name('interests_other')
		->value($data['interests_other'])
		->size(50);

	$edit->addControl('Summarize student preferences/interests as they relate to planning for transition services', 'textarea')
		->width('90%')
		->name('interests_summary')
		->value($data['interests_summary']);

	$edit->addGroup('3. Age Appropriate Transition Assessment(s) performed');
	$edit->addControl('Specify assessment(s) and dates administered', 'textarea')
		->name('assessments')
		->value($data['assessments'])
		->width('90%');

	$edit->addGroup('4. Agency Participation');

	$edit->addControl(FFSelect::factory('Were any outside agencies invited to attend the PPT meeting?'))
		->sql("
			SELECT refid,
			       validvalue
			  FROM webset.glb_validvalues
			 WHERE valuename = 'CT_Transition_Planning' AND ( ((CASE glb_enddate<now() WHEN true THEN 2 ELSE 1 END) = '1') )
			 ORDER BY valuename, sequence_number, validvalue ASC
		")
		->emptyOption(true)
		->name('agencies_invited')
		->value($data['agencies_invited']);

	$edit->addControl('Specify')
		->showIf('agencies_invited', 968)
		->name('agencies_invited_other')
		->value($data['agencies_invited_other'])
		->size(50);

	$edit->addControl(
		FFSwitchYN::factory('If yes, did the agency\'s representative attend?')
			->emptyOption(true)
			->name('agencies_attended')
			->value($data['agencies_attended'])
	);

	$edit->addControl(
		FFSwitchYN::factory('Has any participating agency agreed to provide or pay for services/linkages?')
			->emptyOption(true)
			->name('agency_agreed')
			->value($data['agency_agreed'])
	);

	$edit->addControl('If Yes, specify')
		->showIf('agency_agreed', 'Y')
		->name('agency_agreed_other')
		->value($data['agency_agreed_other'])
		->size(50);

	$edit->addTab('Goals');
	$edit->addGroup('5. Post-School Outcome Goals');
	$edit->addControl('Postsecondary Education or Training', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('post_school_edu')
		->value($data['post_school_edu']);

	$edit->addControl(
		FFSwitchYN::factory('Annual goal(s) and related objectives regarding Postsecondary Education or Training have been developed and are included in this IEP')
			->emptyOption(true)
			->name('post_school_edu_sw')
			->value($data['post_school_edu_sw'])
	);
	$edit->addControl('Employment:', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('post_school_emp')
		->value($data['post_school_emp']);

	$edit->addControl(
		FFSwitchYN::factory('Annual goal(s) and related objectives regarding Employment have been developed and are included in this IEP')
			->emptyOption(true)
			->name('post_school_emp_sw')
			->value($data['post_school_emp_sw'])
	);
	$edit->addControl('Independent Living Skills', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('post_school_liv')
		->value($data['post_school_liv']);

	$edit->addControl(
		FFSwitchYN::factory('Annual goal(s) and related objectives regarding Independent Living Skills have been developed and are included in this IEP')
			->emptyOption(true)
			->help('may include Community Participation')
			->name('post_school_liv_sw')
			->value($data['post_school_liv_sw'])
	);

	$edit->addTab('Course of Study');
	$edit->addGroup('6. Course of Study');
	$edit->addControl(
		FFMultiSelect::factory('Please select ONLY one')
			->data(
				array(
					'1' => 'The course of study needed to assist the child in reaching the transition goals and related objectives will include (including general education activities):',
					'2' => 'Student has completed academic requirements; no academic course of study is required - student\'s IEP includes only transition goals and services.'
				)
			)
			->maxRecords(1)
			->name('course_study')
			->value($data['course_study'])
	);

	$edit->addControl('Specify what related objectives will include', 'textarea')
		->name('course_other')
		->value($data['course_other'])
		->width('90%')
		->disabledIf('course_study', '2');

	$edit->addGroup('Rights under IDEA');
	$edit->addControl(FFIDEAValidValues::factory('CT_Transition_Rights'))
		->caption('Select Please')
		->help('At least one year prior to reaching the age of 18, the student must be informed of her/his rights under IDEA which will transfer at age 18.')
		->name('rights')
		->value($data['rights']);

	$edit->addGroup('Summary of Performance');
	$edit->addControl('Specify date', 'date')
		->help('For a child whose eligibility under special education will terminate the following year due to graduation with a regular education diploma or due to exceeding the age of eligibility, the Summary of Performance will be completed on or before:')
		->name('sop_date')
		->value($data['sop_date']);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->setPresaveCallback('saveData', 'transition_edit.inc.php');
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_in_ts')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();


?>
