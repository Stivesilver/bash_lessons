<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass("edit1", $RefID);

	$edit->title = 'Add/Edit Evaluation Tests';

	$edit->setSourceTable('webset.es_std_scr', 'shsdrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Category', 'select')
		->sqlField('screenid')
		->name('screenid')
		->sql("
			SELECT scrrefid,
				   scrdesc
			  FROM webset.es_statedef_screeningtype
			 WHERE screfid = " . VNDState::factory()->id . "
			 ORDER BY scrseq, scrdesc
		");
	
	$edit->addControl('Test', 'select')
		->sqlField('hsprefid')
		->name('hsprefid')
		->sql("
			SELECT hsprefid, hspdesc
              FROM webset.es_scr_disdef_proc
             WHERE vndrefid= VNDREFID
               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
               AND screenid = VALUE_01
			   AND xml_test is NOT NULL
             ORDER BY 2
		")
		->tie('screenid');
	
	$edit->addControl('Specify if Other:')
		->sqlField('test_name')
		->name('test_name')
		->showIf('hsprefid', db::execSQL("
			SELECT hsprefid
			  FROM webset.es_scr_disdef_proc
		     WHERE vndrefid = VNDREFID
			   AND substring(lower(hspdesc), 1, 5) = 'other'
			")->indexAll()
		)
		->size(50);
	
	$edit->addControl('Date', 'date')
		->sqlField('shsddate');
	
	$edit->addControl('Results', 'textarea')
		->sqlField('shsdhtmltext')
		->css('width', '100%')
		->css('height', '100px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('assessment.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('assessment.php', array('dskey' => $dskey));

	$edit->printEdit();
?>