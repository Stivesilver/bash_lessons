<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$nexttab = io::geti('nexttab');

	$edit = new EditClass('edit1', $stdIEPYear);

	$edit->title = 'Form D - Part 1: State Assessments - NAEP and ACCESS FOR ELLS';

	$edit->setSourceTable('webset.std_form_d', 'syrefid');

	$edit->addGroup('NAEP');

	$edit->addControl(
		FFCheckBox::factory("The student will participate in the NAEP")
			->baseValue('Y')
			->sqlField('naep_map')
	);

	$edit->addControl(
		FFCheckBox::factory("Without accommodations")
			->baseValue('Y')
			->sqlField('naep_o')
	);

	$edit->addControl(
		FFCheckBox::factory("With accommodations")
			->baseValue('Y')
			->sqlField('naep_w')
	);

	$edit->addControl(
		FFCheckBox::factory("The student has been determined eligible for and will participate in the MAP-Alternate (MAP-A); therefore, may be excluded from NAEP participation")
			->baseValue('Y')
			->sqlField('naep_mapa')
	);

	$edit->addGroup('ACCESS FOR ELLS');
	$edit->addControl(
		FFCheckBox::factory("Student will participate in the ACCESS FOR ELLS")
			->baseValue('Y')
			->sqlField('wida_map')
	);

	$edit->addControl(
		FFCheckBox::factory("Without accommodations")
			->baseValue('Y')
			->sqlField('wida_o')
	);

	$edit->addControl(
		FFCheckBox::factory("With accommodations")
			->baseValue('Y')
			->sqlField('wida_w')
	);

	$edit->addControl('ACCESS FOR ELLS Grade', 'select_radio')
		->sqlField('wida_grade')
        ->sql("SELECT refid,
                      validvalue
                 FROM webset.glb_validvalues
                WHERE valuename = 'MO_WIDA_Grades'
                ORDER BY sequence_number")
		->breakRow();

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value($_SESSION['s_userUID'])->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year', 'hidden')->value($stdIEPYear)->sqlField('syrefid');
	$edit->addControl('Sp Considerations ID', 'hidden')->value(io::geti('spconsid'))->name('spconsid');

	$edit->finishURL = 'javascript:parent.switchTab(' . $nexttab . ')';
	$edit->cancelURL = 'javascript:api.window.destroy();';

	$edit->setPostsaveCallback('appAttach', '/apps/idea/iep.mo/spconsid/srv_spconsid.inc.php');

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '40%';

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
