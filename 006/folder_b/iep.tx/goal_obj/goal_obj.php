<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Progress Reporting';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '60%';

	$edit->setSourceTable('webset_tx.std_goal_progress', 'iepyear');

	$edit->addGroup('General Information');

	$edit->addControl(
		FFSwitchYN::factory('Prior to determining placement, the ARD/IEP committee developed IEP goals and objectives and/or accommodations based on consideration of current assessment and the student\'s educational needs')
			->sqlField('field0')
			->emptyOption(TRUE)
	);

	$edit->addControl('Schedule for evaluation of goals and objectives', 'select')
		->sqlField('field1_bas')
		->name('field1_bas')
		->sql("
			SELECT go_sub_refid, go_sub_desc
			  FROM webset_tx.def_goalobj_dtl
			 WHERE go_refid=1
			   AND (end_date>now() OR end_date IS NULL)
		");

	        
    $edit->addControl('Specify Other')
		->sqlField('field1_oth')
        ->showIf('field1_bas', db::execSQL("
                                  SELECT go_sub_refid
                                    FROM webset_tx.def_goalobj_dtl
                                   WHERE SUBSTRING(LOWER(go_sub_desc), 1, 5) = 'other'
                                 ")->indexAll())
        ->size(50);

	$edit->addControl('IEP progress will be reported to parent(s)', 'select')
		->sqlField('field2_bas')
		->name('field2_bas')
		->sql("
			SELECT go_sub_refid, go_sub_desc
			  FROM webset_tx.def_goalobj_dtl
			 WHERE go_refid=2
			   AND (end_date>now() OR end_date IS NULL)
		");

	        
    $edit->addControl('Specify Other')
		->sqlField('field2_oth')
        ->showIf('field2_bas', db::execSQL("
                                  SELECT go_sub_refid
                                    FROM webset_tx.def_goalobj_dtl
                                   WHERE SUBSTRING(LOWER(go_sub_desc), 1, 5) = 'other'
                                 ")->indexAll())
        ->size(50);

	$edit->addControl('Parents will be informed by', 'select')
		->sqlField('field3_bas')
		->name('field3_bas')
		->sql("
			SELECT go_sub_refid, go_sub_desc
			  FROM webset_tx.def_goalobj_dtl
			 WHERE go_refid=3
			   AND (end_date>now() OR end_date IS NULL)
		");

	        
    $edit->addControl('Specify Other')
		->sqlField('field3_oth')
        ->showIf('field3_bas', db::execSQL("
                                  SELECT go_sub_refid
                                    FROM webset_tx.def_goalobj_dtl
                                   WHERE SUBSTRING(LOWER(go_sub_desc), 1, 5) = 'other'
                                 ")->indexAll())
        ->size(50);
	
	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_goal_progress')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>