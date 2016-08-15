<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$nexttab = io::geti('nexttab');

	$SQL = "
        SELECT refid,
               studentage
          FROM webset.std_form_d
         WHERE stdrefid = " . $tsRefID . "
           AND syrefid  = " . $stdIEPYear . "
    ";
	$data = db::execSQL($SQL)->assoc();

	$edit = new EditClass('edit1', $stdIEPYear);

	$edit->title = 'Form D - Part 1: State Assessments';

	$edit->setSourceTable('webset.std_form_d', 'syrefid');


	$edit->addGroup("Grades 3-8: Assessment");

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the Grade-Level Assessment")
			->baseValue('Y')
			->sqlField('young_map')
	);

	$edit->addControl(
		FFCheckBox::factory("Without accommodations")
			->baseValue('Y')
			->sqlField('young_o')
	);

	$edit->addControl(
		FFCheckBox::factory("With accommodations (complete Part 2)")
			->baseValue('Y')
			->sqlField('young_w')
	);

	$edit->addControl(
		FFCheckBox::factory("The student has been determined eligible for and will participate in the MAP-Alternate (MAP-A); therefore, is excluded from Grade-Level Assessment participation. (complete Part 4)")
			->baseValue('Y')
			->sqlField('young_map_a')
	);

	$edit->addGroup("Grades 9-12: Assessment");

	$edit->addControl(
		FFCheckBox::factory($data['studentage'] == 1 ? "The student will participate in the Grade-Level Assessment" : "The student will participate in required End of Course (EOC) Assessments (*See Note 1 below)")
			->baseValue('Y')
			->sqlField('assessments')
	);

	$edit->addControl(
		FFCheckBox::factory("Without accommodations")
			->baseValue('Y')
			->sqlField('accommparticip')
	);

	$edit->addControl(
		FFCheckBox::factory("With accommodations (complete Part 2)")
			->baseValue('Y')
			->sqlField('accommmath')
	);

	$edit->addGroup("Grades 9-12: Additional EOC Assessment");

	$edit->addControl(
		FFCheckBox::factory("The IEP team has determined the student will participate in the following optional LEA EOC Assessment(s) (*See Note 2 below)")
			->baseValue('Y')
			->sqlField('addeoc_m')
	);


	$edit->addControl("Specify Assessments", "textarea")
		->sqlField('addeoc_ass')
		->css("width", "100%")
		->css("height", "30px");

	$edit->addControl(
		FFCheckBox::factory("Without accommodations")
			->baseValue('Y')
			->sqlField('addeoc_o')
	);

	$edit->addControl("Without accommodations for", "select_check")
		->sqlField('addeoc_os')
		->displaySelectAllButton(false)
		->data(
			array(
				'15' => 'Geometry',
				'11' => 'Algebra II',
				'19' => 'English I',
				'12' => 'American History'
			)
		);

	$edit->addControl(
		FFCheckBox::factory("With accommodations (complete Part 2)")
			->baseValue('Y')
			->sqlField('addeoc_w')
	);

	$edit->addControl("With accommodations for", "select_check")
		->sqlField('addeoc_ws')
		->displaySelectAllButton(false)
		->data(
			array(
				'15' => 'Geometry',
				'11' => 'Algebra II',
				'19' => 'English I',
				'12' => 'American History'
			)
		);


	$edit->addGroup("Grades 9-12: Exempt");

	$edit->addControl(
		FFCheckBox::factory("The IEP team has determined the student is exempt from the following Additional EOC Assessment(s):")
			->baseValue('Y')
			->sqlField('exempt')
	);

	$edit->addControl("Specify Assessments", "textarea")
		->sqlField('exempt_ass')
		->css("width", "100%")
		->css("height", "30px");


	$edit->addControl("The IEP team has determined the student is exempt from the following additional LEA EOC Assessment(s):", "select_check")
		->sqlField('exempts')
		->displaySelectAllButton(false)
		->data(
			array(
				'15' => 'Geometry',
				'11' => 'Algebra II',
				'19' => 'English I',
				'12' => 'American History'
			)
		);

	$edit->addControl(
		FFCheckBox::factory("The student has been determined eligible for and will
			participate in the MAP-Alternate for grade 11 (MAP-A);
			therefore is excluded from EOC participation.
			 (complete Part 4)
		")->baseValue('Y')
			->sqlField('eligible')
	);

	$edit->addGroup("ACT®: Students in Grade11");

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the ACT®")
			->baseValue('Y')
			->sqlField('act')
	);

	$edit->addControl(
		FFCheckBox::factory("Without accommodations")
			->baseValue('Y')
			->sqlField('act_o')
	);

	$edit->addControl(
		FFCheckBox::factory("With accommodations (complete Part 3)")
			->baseValue('Y')
			->sqlField('act_w')
	);

	$edit->addControl(
		FFCheckBox::factory("The student has been determined eligible for and will
			participate in the MAP-Alternate for grade 11 (MAP-A);
			therefore is excluded from ACT® participation.
			(complete Part 4)")
			->baseValue('Y')
			->sqlField('act_mapa')
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
	$edit->firstCellWidth = '50%';

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
