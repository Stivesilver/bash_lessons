<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	$edit = new EditClass("edit1", $RefID);

	$edit->topButtons = true;

	$edit->title = 'Add/Edit Observation';

	$edit->setSourceTable('webset.es_std_er_observation', 'erob_refid');

	$edit->addGroup('General Information');

	$edit->addControl('Observer', 'text')->sqlField('observer')->width('600px');

	$edit->addControl('Position/Role of Observer', 'text')->sqlField('role')->width('600px');

	$edit->addControl('Location of Observation', 'text')->sqlField('location')->width('600px');

	$edit->addControl('Date', 'date')->sqlField('date');

	$edit->addControl('Time', 'text')->sqlField('time')->width('600px');

	$edit->addControl('Type of activities observed')->width('100%')->sqlField('activities_type');

	$edit->addControl(FFCheckBoxList::factory('Observation conducted in area of concern(s)'))
		->sql(IDEADef::getValidValueSql("EVAL_Observation", "refid, validvalue"))
		->sqlField('conducted')
		->name('conducted')
		->displaySelectAllButton(false)
		->breakRow();

	$edit->addControl('Other')
		->sqlField('conductedoth')
		->name('conductedoth')
		->showIf('conducted', db::execSQL("
                                  SELECT refid
                                    FROM webset.glb_validvalues
                                   WHERE SUBSTRING(LOWER(validvalue), 1, 5) = 'other'
								     AND valuename = 'EVAL_Observation'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Observation', 'textarea')
		->css('height', '300px')
		->css('width', '100%')
		->help("REQUIRED for suspected disability categories of Autism, Emotional Disturbance, and Specific Learning Disability. OPTIONAL for all other suspected categories of disability")
		->sqlField('summary');

	$edit->addControl("Order #", "hidden")
		->sqlField('order_num')
		->value(
			(int)db::execSQL("
			SELECT max(order_num)
			  FROM webset.es_std_er_observation
			 WHERE eprefid = " . $evalproc_id . "
			")->getOne() + 1
		);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');

	$edit->finishURL = CoreUtils::getURL('./observation_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('./observation_list.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
