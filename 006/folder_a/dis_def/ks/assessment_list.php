<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'State-wide Assessments';

	$list->showSearchFields = true;

	$list->SQL = "
        SELECT swarefid,
               swaseq,
               swadesc,
               CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END  as status
	      FROM webset.statedef_assess_state
	     WHERE screfid = " . VNDState::factory()->id . "
         ORDER BY swaseq, swadesc
    ";

	$list->addSearchField('Assessment', "LOWER(swadesc)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField(
		FFIDEAStatus::factory()
			->sqlField("CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END")
	);

	$list->addColumn('Order #');
	$list->addColumn('Assessment');
	$list->addColumn('Active')->type('switch');

	$list->addURL = 'assessment_edit.php';
	$list->editURL = 'assessment_edit.php';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_assess_state')
			->setKeyField('swarefid')
			->applyListClassMode()
	);

	$list->printList();
?>