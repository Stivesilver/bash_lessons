<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$area_id = IDEAAppArea::ID_SEC_TRANS_ACTIVITIES;

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Transition Activities';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->finishURL = CoreUtils::getURL('transitions.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('transitions.php', array('dskey' => $dskey));

	$edit->addGroup('General Information');

	$edit->addControl('Order #', 'integer')
		->sqlField('order_num')
		->value(
			(int) db::execSQL("
					SELECT max(order_num)
					  FROM webset.std_general
					 WHERE iepyear = " . $stdIEPYear . "
					   AND area_id = " . $area_id . "
	            ")->getOne() + 1
		)
		->size(5);

	$edit->addControl('Transition Activity', 'select')
		->sqlField('int01')
		->name('int01')
		->sql(IDEADef::getValidValueSql('ID_Transition_Activity', "refid, validvalueid || '. ' || validvalue"));

	$edit->addControl(
		FFIDEASchoolYear::factory()
			->sqlField('int03')
	);

	$edit->addControl('Description', 'textarea')
		->sqlField('txt01')
		->autoHeight(true);

	$edit->addControl('Position Responsible')		
		->sqlField('txt02')
		->css('width', '90%');

	$edit->addControl('Start Date', 'date')
		->sqlField('dat01');

	$edit->addControl('Status', 'select')
		->sqlField('int02')
		->name('int02')
		->sql(IDEADef::getValidValueSql('ID_Transition_Status', "refid, sequence_number || ' - ' || validvalue"));
	
	$edit->addControl('Specify Status/Why', 'textarea')
		->sqlField('txt03')
		->css('width', '100%')
		->css('height', '100px')
		->showIf('int02',  db::execSQL("
			SELECT refid
			  FROM webset.glb_validvalues
			 WHERE valuename = 'ID_Transition_Status'
			   AND validvalueid = 'S'
			")->indexAll()
		)
		->autoHeight(true);

	$edit->addControl('Completion Date', 'date')
		->sqlField('dat02');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("Area ID", "hidden")->value($area_id)->sqlField('area_id');

	$edit->printEdit();
?>
