<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Assessment Accommodations';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT stsrefid,
		       aacdesc,
		       stsdesc,
		       CASE WHEN NOW() > accmod.recdeactivationdt THEN 'N' ELSE 'Y' END  as status
          FROM webset.statedef_mod_acc accmod
               LEFT OUTER JOIN webset.statedef_assess_acc_cat cat ON cat.aacrefid = accmod.aacrefid
         WHERE accmod.screfid = " . VNDState::factory()->id . "
           AND UPPER(assessmentsw) = 'Y'
         ORDER BY aacdesc, stsdesc
    ";

	$list->addSearchField('Area', 'cat.aacrefid', 'select')
		->sql("
			SELECT aacrefid,
			       aacdesc
			  FROM webset.statedef_assess_acc_cat
			 WHERE screfid = " . VNDState::factory()->id . "
			 ORDER BY aacdesc
		");
	$list->addSearchField('Assessments Accommodations', "LOWER(stsdesc)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField(
		FFIDEAStatus::factory()
			->sqlField("CASE WHEN NOW() > accmod.recdeactivationdt THEN 'N' ELSE 'Y' END")
	);

	$list->addColumn('Area')->type('group');
	$list->addColumn('Assessments Accommodations');
	$list->addColumn('Active')->type('switch');

	$list->addURL = 'accmods_edit.php';
	$list->editURL = 'accmods_edit.php';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_mod_acc')
			->setKeyField('stsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>