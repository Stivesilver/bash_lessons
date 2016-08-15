<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$student = IDEAStudent::factory($tsRefID);
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass('edit1', $tsRefID);

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

	$edit->title = 'Cover Page';
	$edit->firstCellWidth = '40%';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addGroup('General Information');

	$edit->addControl('Current Enrolled (Attending) School', 'protected')
		->value($student->get('vouname'));

	$edit->addControl('Current Home (Resident) School', 'protected')
		->value($student->get('vouname_res'));

	$edit->addControl('Age', 'protected')
		->value($student->get('stdage'));

	$edit->addControl('Current Grade', 'protected')
		->value($student->get('grdlevel'));


	$edit->addControl('Gender', 'protected')
		->value($student->get('stdsex'));

	$edit->addControl(FFSwitchYN::factory())
		->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No'),
				array('A', 'N/A')
			)
		)
		->name('high_school')
		->caption('If your school district does not have its own high school, is the student attending his/her designated high school?')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'ct_iep', 'cover_page_high_school', $stdIEPYear));

	$edit->addGroup('Next Year');
	$edit->addControl(FFIDEAGradeLevel::factory())
		->sqlField('nsy_gl_refid')
		->caption('Grade Next Year')
		->emptyOption(true);

	$edit->addControl('H.S. Credits')
		->name('hscredits')
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'ct_iep', 'hscredits', $stdIEPYear))
		->css('width', '100%');

	$edit->addControl(FFIDEASchool::factory(true))
		->sqlField('nsy_attsch')
		->caption('School Next Year')
		->emptyOption(true);

	$edit->addControl(FFIDEASchool::factory(true))
		->sqlField('nsy_ressch')
		->caption('Home School Next Year')
		->emptyOption(true);

	$edit->addGroup('Student Demographics');

	$edit->addControl('SASID #', 'protected')
		->value($student->get('stdstateidnmbr'));

	$edit->addControl('Case Manager', 'protected')
		->value($student->get('cmname'));

	$edit->addControl('Student Address', 'protected')
		->value($student->get('stdaddress'));

	$edit->addControl('Student Instructional Lang', 'protected')
		->value($student->get('prim_lang'));

	$edit->addControl('Home Dominant Lang', 'protected')
		->value($student->get('home_lang'));

	$edit->addControl('Student Home Phone', 'protected')
		->value($student->get('stdhphn'));

	$edit->addControl('tsRefID', 'hidden')
		->name('tsRefID')
		->value($tsRefID);

	$edit->addControl('stdIEPYear', 'hidden')
		->name('stdIEPYear')
		->value($stdIEPYear);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->setPresaveCallback('update_cover_page', 'cover_page_edit.inc.php');

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyEditClassMode()
	);

	$edit->printEdit();
?>
