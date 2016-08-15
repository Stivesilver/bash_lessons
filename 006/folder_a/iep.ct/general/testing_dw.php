<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudentCT($tsRefID);

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->firstCellWidth = '40%';
	$edit->setSourceTable('webset.std_assess_state', 'iepyear');

	$edit->title = 'State and District Testing and Accommodations';

	$edit->saveLocal = false;
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$data = $student->getTesting();

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
	$edit->addControl(
		FFSwitchYN::factory('Select all appropriate options.')
			->emptyOption(true, 'N/A')
			->data(array('N' => 'Alternate Assessment(s)'))
			->name('distr_assessment')
			->value($data['distr_assessment'])
	);

	$edit->addControl('Alternate Assessment(s)', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('aleternativ_assessment')
		->showIf('distr_assessment', 'N')
		->help('Alternate assessments must be specified and a statement provided for each as to why the child cannot participate in the standard assessment and why the particular alternate assessment selected is appropriate for the child.')
		->value($data['aleternativ_assessment']);

	$edit->addControl(
		FFSwitchYN::factory('Select all appropriate options.')
			->emptyOption(true, 'No accommodations will be provided, OR')
			->data(array('Y' => 'Accommodations will be provided as specified on Page 8, OR', 'N' =>'Accommodations will be provided as specified below.'))
			->name('distr_accommodation')
			->value($data['distr_accommodation'])
	);

	$edit->addControl('Accommodations', 'textarea')
		->css('width', '90%')
		->css('height', '80px')
		->name('aleternativ_accommodation')
		->showIf('distr_accommodation', 'N')
		->value($data['aleternativ_accommodation']);


	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_assess_state')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->setPresaveCallback('saveData', 'testing_edit.inc.php', array('mode' => 1));
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>
