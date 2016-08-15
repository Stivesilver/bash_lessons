<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$nexttab = io::geti('nexttab');

	$edit = new EditClass('edit1', $stdIEPYear);

	$edit->title = 'Form D - Part 1: State Assessments';
	$edit->topButtons = true;

	$edit->setSourceTable('webset.std_form_d', 'syrefid');


	$edit->addGroup("Grades 3-8: Grade-Level Assessment");

	$edit->addControl(
		FFCheckBox::factory("Grades 3-8: Grade-Level Assessment")
			->baseValue('Y')
			->sqlField('young_map')
	);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the Grade-Level Assessment WITHOUT accommodations.")
			->baseValue('Y')
			->sqlField('young_o')
	);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the Grade-Level Assessment WITH accommodations. (Complete Part 2A)")
			->baseValue('Y')
			->sqlField('young_w')
	);

	$edit->addControl(
		FFCheckBox::factory("The student has been determined eligible for and will participate in the MAP-Alternate (MAP-A); therefore, is excluded from Grade-Level Assessment participation. (Complete Part 4)")
			->baseValue('Y')
			->sqlField('young_map_a')
	);

	$edit->addGroup("Grades 9-12 or, if appropriate, earlier grades: End-of-Course (EOC) Assessment");

	$edit->addControl(
		FFCheckBox::factory("Grades 9-12 or, if appropriate, earlier grades: End-of-Course (EOC) Assessment")
			->baseValue('Y')
			->sqlField('assessments')
	);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in these End-of-Course Assessments WITHOUT accommodations.")
			->baseValue('Y')
			->sqlField('addeoc_o')
	);

	/**
	 * ID of webset.statedef_aa_prog
	 * @var FFDateTime
	 * */
	$subjects_eoc = array(
		'8' => 'Algebra I',
		'9' => 'Biology',
		'10' => 'English II',
		'18' => 'Government',
		'11' => 'Algebra II',
		'15' => 'Geometry',
		'12' => 'American History',
		'19' => 'English I',
		'6' => 'Physical Science'
	);

	$edit->addControl("WITHOUT accommodations for", "select_check")
		->sqlField('addeoc_os')
		->displaySelectAllButton(false)
		->data($subjects_eoc);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in these End-of-Course Assessments WITH accommodations. (Complete Part 2B)")
			->baseValue('Y')
			->sqlField('addeoc_w')
	);

	$edit->addControl("WITH accommodations for", "select_check")
		->sqlField('addeoc_ws')
		->displaySelectAllButton(false)
		->data($subjects_eoc);

	$edit->addControl(
		FFCheckBox::factory("The IEP team has determined the student is exempt from these optional EOC Assessments:")
			->baseValue('Y')
			->sqlField('exempt')
	);

	$edit->addControl("Specify Assessments", "textarea")
		->sqlField('exempt_ass')
		->css("width", "100%")
		->css("height", "30px");


	/**
	 * ID of webset.statedef_aa_prog
	 * @var FFDateTime
	 * */
	$subjects_exmpt = array(
		'11' => 'Algebra II',
		'12' => 'American History',
		'19' => 'English I',
		'15' => 'Geometry',
		'6' => 'Physical Science'
	);

	$edit->addControl("Exempt accommodations", "select_check")
		->sqlField('exempts')
		->displaySelectAllButton(false)
		->data($subjects_exmpt);

	$edit->addControl(
		FFCheckBox::factory("The student has been determined to be eligible for and will participate in the MAP-Alternate (MAP-A); therefore, is excluded from End-of-Course Assessment participation. (Complete Part 4) ")
			->baseValue('Y')
			->sqlField('eligible')
	);

	$edit->addGroup('Grades 4, 8 and 12: If selected for the National Assessment of Educational Progress (NAEP)');

	$edit->addControl(
		FFCheckBox::factory("Grades 4, 8 and 12: If selected for the National Assessment of Educational Progress (NAEP)")
			->baseValue('Y')
			->sqlField('naep_map')
	);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the NAEP Assessment, if selected, WITHOUT accommodations.")
			->baseValue('Y')
			->sqlField('naep_o')
	);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the NAEP Assessment, if selected) WITH accommodations. (See NAEP Notes)")
			->baseValue('Y')
			->sqlField('naep_w')
	);

	$edit->addControl(
		FFCheckBox::factory("The student has been determined eligible for and will participate in the MAP-Alternate (MAP-A); therefore, is excluded from NAEP Assessment participation. (Complete Part 4)")
			->baseValue('Y')
			->sqlField('naep_mapa')
	);
	$edit->addGroup("Grade 11: ACT®");

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the ACT Assessment. (Complete Part 3)")
			->baseValue('Y')
			->sqlField('act')
	);

	$edit->addControl(
		FFCheckBox::factory("The student has been determined eligible for and will participate in the MAP-Alternate (MAP-A); therefore, is excluded from ACT Assessment participation. (Complete Part 4)")
			->baseValue('Y')
			->sqlField('act_mapa')
	);

	$edit->addGroup('K-12 ELL students (students marked LEP-RCV or LEP-NRC in Core Data): ACCESS For ELLs 2.0');
	$edit->addControl(
		FFCheckBox::factory("K-12 ELL students (students marked LEP-RCV or LEP-NRC in Core Data): ACCESS For ELLs 2.0")
			->baseValue('Y')
			->sqlField('wida_map')
	);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the ACCESS For ELLs 2.0 Assessment WITHOUT accommodations.")
			->baseValue('Y')
			->sqlField('wida_o')
	);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the ACCESS for ELLs 2.0 Assessment WITH accommodations. (Complete Part 5)")
			->baseValue('Y')
			->sqlField('wida_w')
	);

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the Alternate ACCESS for ELLs 2.0 Assessment. (For those who do or would qualify for MAP-A; complete Part 4)")
			->baseValue('Y')
			->sqlField('wida_alt')
	);

	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
	$edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
	$edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');
	$edit->addControl("Sp Considerations ID", "hidden")->value(io::geti('spconsid'))->name('spconsid');

	$edit->finishURL = 'javascript:parent.switchTab(' . $nexttab . ')';
	$edit->cancelURL = 'javascript:api.window.destroy();';

	$edit->setPostsaveCallback('appAttach', '/apps/idea/iep.mo/spconsid/srv_spconsid.inc.php');

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '70%';

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_form_d')
			->setKeyField('syrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

	include("notes1.php");
	include("notes0.php");
?>
