<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Program Modifications and Accommodations';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT stsrefid,
		       macdesc,
		       stsdesc,
		       CASE WHEN NOW() > progmod.recdeactivationdt THEN 'N' ELSE 'Y' END  as status
          FROM webset.statedef_mod_acc progmod
               LEFT OUTER JOIN webset.statedef_mod_acc_cat cat ON cat.macrefid = progmod.macrefid
         WHERE progmod.screfid = " . VNDState::factory()->id . "
           AND UPPER(modaccommodationsw) = 'Y'
         ORDER BY macdesc, stscode, stsdesc
    ";


	$list->addSearchField('Area', 'cat.macrefid', 'select')
		->sql("
			SELECT macrefid,
			       macdesc
			  FROM webset.statedef_mod_acc_cat
			 WHERE screfid = " . VNDState::factory()->id . "
			 ORDER BY macdesc
		");
	$list->addSearchField('Program Modification and Accommodation', "LOWER(stsdesc)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField(
		FFIDEAStatus::factory()
			->sqlField("CASE WHEN NOW() > progmod.recdeactivationdt THEN 'N' ELSE 'Y' END")
	);

	$list->addColumn('Area')->type('group');
	$list->addColumn('Program Modification and Accommodation');
	$list->addColumn('Active')->type('switch');

	$list->addURL = 'progmods_edit.php';
	$list->editURL = 'progmods_edit.php';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.statedef_mod_acc')
			->setKeyField('stsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>