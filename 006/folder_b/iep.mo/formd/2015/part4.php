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
		   AND syrefid = " . $stdIEPYear . "
	";

	$data = db::execSQL($SQL)->assoc();

	$edit = new EditClass('edit1', $stdIEPYear);

	$edit->title = 'Form D - Part 4: Alternate Assessment (MAP-A)';

	$edit->setSourceTable('webset.std_form_d', 'syrefid');

	$edit->addGroup("Justification for Participation in the Alternate Assessment (MAP-A)");

	$edit->addObject(
		UILayout::factory()
			->newLine()
			->addHTML('')
			->newLine()
			->addHTML('The justification for why the child cannot participate in the general education assessment (Grade-Level or EOC) is based upon the multiple criteria for eligibility to participate in the MAP-A and is described below (must complete all four sections):', 'italic')
			->newLine()
			->addHTML('')
	);

	$edit->addControl("Describe how the student demonstrates the most significant cognitive disabilities and limited adaptive skills that may be combined with physical or behavioral limitations", "textarea")
		->sqlField('statement')
		->css("width", "100%")
		->css("height", "100px")
		->showIf('eligible', 'Y');

	$edit->addControl("Describe how the most significant cognitive disability impacts the student's access to the curriculum and requires specialized instruction", "textarea")
		->sqlField('alternate')
		->css("width", "100%")
		->css("height", "100px")
		->showIf('eligible', 'Y');

	$edit->addControl("Describe how the most significant cognitive disability impacts the student's post-school outcomes", "textarea")
		->sqlField('statement_map')
		->css("width", "100%")
		->css("height", "100px")
		->showIf('eligible', 'Y');

	$edit->addControl("Describe any additional factors considered. The student's inability to participate in the general education assessment must be primarily the result of the most significant cognitive disability and NOT excessive absences; visual or auditory disabilities; or social, cultural, language or economic differences", "textarea")
		->sqlField('assessments_map')
		->css("width", "100%")
		->css("height", "100px")
		->showIf('eligible', 'Y');

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

	include("notes4.php");
	include("notes0.php");
?>
